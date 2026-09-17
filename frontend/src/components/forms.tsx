"use client";

import { useActionState, type InputHTMLAttributes } from "react";
import { signIn, signUp, updateProfile, uploadMedia, deleteMedia } from "@/app/actions";
import type { FormState } from "@/lib/contracts";

function Field({ label, error, ...props }: InputHTMLAttributes<HTMLInputElement> & { name: string; label: string; error?: string[] }) {
  return <div className="field"><label htmlFor={props.name}>{label}</label><input {...props} id={props.name} aria-invalid={Boolean(error)} aria-describedby={error ? `${props.name}-error` : undefined} />{error && <p className="error" id={`${props.name}-error`}>{error[0]}</p>}</div>;
}
function Feedback({ state }: { state: FormState }) {
  return state.message ? <p className={state.success ? "notice" : "error"} role={state.success ? "status" : "alert"}>{state.message}</p> : null;
}
export function AuthForm({ register = false }: { register?: boolean }) {
  const [state, action, pending] = useActionState(register ? signUp : signIn, {});
  return <form action={action} className="stack"><Feedback state={state} />
    {register && <Field name="name" label="Full name" required autoComplete="name" error={state.errors?.name} />}
    <Field name="email" label="Email address" type="email" required autoComplete="username" error={state.errors?.email} />
    <Field name="password" label="Password" type="password" required minLength={register ? 12 : undefined} autoComplete={register ? "new-password" : "current-password"} error={state.errors?.password} />
    {register && <><p className="muted">Use at least 12 characters with letters and numbers.</p><Field name="password_confirmation" label="Confirm password" type="password" required autoComplete="new-password" error={state.errors?.password_confirmation} /></>}
    <button className="btn" disabled={pending}>{pending ? "Please wait…" : register ? "Create account" : "Sign in"}</button>
  </form>;
}
export function ProfileForm({ name }: { name: string }) {
  const [state, action, pending] = useActionState(updateProfile, {});
  return <form action={action} className="stack"><Feedback state={state} /><Field name="name" label="Full name" defaultValue={name} required error={state.errors?.name} /><div><button className="btn" disabled={pending}>{pending ? "Saving…" : "Save profile"}</button></div></form>;
}
export function UploadForm() {
  const [state, action, pending] = useActionState(uploadMedia, {});
  return <form action={action} className="stack"><Feedback state={state} /><Field name="file" label="Image or document" type="file" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.txt,.csv" required error={state.errors?.file} /><p className="muted">Images, PDF, text or CSV. Maximum 8 MB.</p><div><button className="btn" disabled={pending}>{pending ? "Uploading…" : "Upload file"}</button></div></form>;
}
export function DeleteFile({ id }: { id: number }) {
  const [state, action, pending] = useActionState(deleteMedia.bind(null, id), {});
  return <form action={action}><Feedback state={state} /><button className="text-link" disabled={pending}>{pending ? "Deleting…" : "Delete"}</button></form>;
}
