import "server-only";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import { ApiError, apiRequest } from "@/lib/api";
import { userResponseSchema } from "@/lib/contracts";

const COOKIE = "starter_session";
export async function sessionToken(): Promise<string | undefined> {
  return (await cookies()).get(COOKIE)?.value;
}
export async function saveSession(token: string, expiresAt: string): Promise<void> {
  (await cookies()).set(COOKIE, token, {
    httpOnly: true, secure: process.env.NODE_ENV === "production", sameSite: "lax",
    path: "/", expires: new Date(expiresAt),
  });
}
export async function clearSession(): Promise<void> { (await cookies()).delete(COOKIE); }
export async function currentUser() {
  const token = await sessionToken();
  if (!token) return null;
  try { return (await apiRequest("/me", userResponseSchema, { token })).data; }
  catch (error) {
    if (error instanceof ApiError && (error.status === 401 || error.status === 403)) return null;
    throw error;
  }
}
export async function requireSession(): Promise<string> {
  const token = await sessionToken();
  if (!token) redirect("/login");
  return token;
}
