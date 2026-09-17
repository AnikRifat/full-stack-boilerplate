"use server";

import { z } from "zod";
import { redirect } from "next/navigation";
import { revalidatePath } from "next/cache";
import { ApiError, apiRequest } from "@/lib/api";
import { authResponseSchema, userResponseSchema, mediaResponseSchema, type FormState } from "@/lib/contracts";
import { clearSession, saveSession, requireSession, sessionToken } from "@/lib/session";

const credentials = z.object({ email: z.string().trim().toLowerCase().email().max(255), password: z.string().min(1).max(255) });
const registration = credentials.extend({ name: z.string().trim().min(1).max(255), password: z.string().min(12).max(255).regex(/[a-zA-Z]/).regex(/[0-9]/), password_confirmation: z.string() })
  .refine(data => data.password === data.password_confirmation, { path: ["password_confirmation"], message: "Passwords do not match." });
function formError(error: unknown): FormState {
  if (error instanceof ApiError) return { message: error.message, errors: error.fields };
  throw error;
}
async function authenticate(form: FormData, register: boolean): Promise<FormState> {
  const input = { email: form.get("email"), password: form.get("password"), name: form.get("name"), password_confirmation: form.get("password_confirmation") };
  const parsed = (register ? registration : credentials).safeParse(input);
  if (!parsed.success) return { errors: z.flattenError(parsed.error).fieldErrors, message: "Check the highlighted fields." };
  try {
    const result = await apiRequest(register ? "/auth/register" : "/auth/login", authResponseSchema, { method: "POST", body: parsed.data });
    await saveSession(result.data.token, result.data.expires_at);
  } catch (error) { return formError(error); }
  redirect("/account");
}
export async function signIn(_previous: FormState, form: FormData): Promise<FormState> { return authenticate(form, false); }
export async function signUp(_previous: FormState, form: FormData): Promise<FormState> { return authenticate(form, true); }
export async function signOut(): Promise<void> {
  const token = await sessionToken();
  let remoteRevoked = true;
  try { if (token) await apiRequest("/auth/logout", z.null(), { method: "POST", token }); }
  catch (error) {
    if (!(error instanceof ApiError)) throw error;
    remoteRevoked = error.status === 401;
  }
  await clearSession();
  redirect(remoteRevoked ? "/login" : "/login?logout=local");
}
export async function updateProfile(_previous: FormState, form: FormData): Promise<FormState> {
  const token = await requireSession();
  const parsed = z.object({ name: z.string().trim().min(1).max(255) }).safeParse({ name: form.get("name") });
  if (!parsed.success) return { errors: z.flattenError(parsed.error).fieldErrors };
  try { await apiRequest("/me", userResponseSchema, { method: "PATCH", token, body: parsed.data }); }
  catch (error) { return formError(error); }
  revalidatePath("/account");
  return { success: true, message: "Profile saved." };
}
export async function uploadMedia(_previous: FormState, form: FormData): Promise<FormState> {
  const token = await requireSession();
  const file = form.get("file");
  if (!(file instanceof File) || file.size === 0 || file.size > 8 * 1024 * 1024) return { errors: { file: ["Choose a file up to 8 MB."] } };
  const body = new FormData();
  body.set("file", file);
  body.set("collection", "document");
  try { await apiRequest("/media", mediaResponseSchema, { method: "POST", token, body }); }
  catch (error) { return formError(error); }
  revalidatePath("/account");
  return { success: true, message: "File uploaded." };
}
export async function deleteMedia(id: number): Promise<FormState> {
  const token = await requireSession();
  if (!Number.isSafeInteger(id) || id <= 0) return { message: "Invalid file." };
  try { await apiRequest(`/media/${id}`, z.null(), { method: "DELETE", token }); }
  catch (error) { return formError(error); }
  revalidatePath("/account");
  return { success: true, message: "File deleted." };
}
