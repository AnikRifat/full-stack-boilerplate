import Link from "next/link";
import { AuthForm } from "@/components/forms";

export default async function Login({ searchParams }: { searchParams: Promise<{ logout?: string }> }) {
  const { logout } = await searchParams;
  return <div className="auth-card panel stack"><p className="eyebrow">Your account</p><h1>Welcome back</h1><p className="muted">Sign in to your account.</p>{logout === "local" && <p className="notice" role="status">Signed out on this browser. The remote session could not be revoked and will expire automatically.</p>}<AuthForm /><p className="muted">New here? <Link className="text-link" href="/register">Create an account</Link></p></div>;
}
