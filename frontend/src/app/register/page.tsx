import Link from "next/link";
import { AuthForm } from "@/components/forms";
import { apiRequest } from "@/lib/api";
import { configurationSchema } from "@/lib/contracts";

export default async function Register() {
  const { data } = await apiRequest("/configuration", configurationSchema);
  return <div className="auth-card panel stack"><p className="eyebrow">Get started</p><h1>Create your account</h1>{data.registration_enabled ? <AuthForm register /> : <p className="notice">Registration is currently closed. Contact your administrator for access.</p>}<p className="muted">Already have an account? <Link className="text-link" href="/login">Sign in</Link></p></div>;
}
