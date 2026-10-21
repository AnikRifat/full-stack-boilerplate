import { redirect } from "next/navigation";
import { signOut } from "@/app/actions";
import { DeleteFile, ProfileForm, UploadForm } from "@/components/forms";
import { currentUser, requireSession } from "@/lib/session";
import { apiRequest } from "@/lib/api";
import { mediaListSchema } from "@/lib/contracts";

export default async function Account() {
  const user = await currentUser();
  if (!user) redirect("/login");
  const token = await requireSession();
  const files = user.permissions.includes("media.view") ? (await apiRequest("/media", mediaListSchema, { token })).data : [];
  return <section className="account"><div className="flex flex-wrap items-center justify-between gap-4"><div className="stack"><p className="eyebrow">Your workspace</p><h1>Hello, {user.name}</h1><p className="muted">{user.email}</p></div><form action={signOut}><button className="btn btn-secondary">Sign out</button></form></div>
    <div className="account-grid"><div className="panel"><h2>Profile</h2><ProfileForm name={user.name} /></div>
      {user.permissions.includes("media.view") && <div className="panel stack"><h2>Your files</h2>{user.permissions.includes("media.upload") && <UploadForm />}{files.length === 0 ? <p className="muted">No files uploaded yet.</p> : files.map(file => <div className="file-row" key={file.id}><div><p className="file-name">{file.filename}</p><p className="muted">{(file.size / 1024).toFixed(1)} KB</p></div><div className="flex items-center gap-4"><a className="text-link" href={file.url} target="_blank" rel="noopener noreferrer">Open</a>{user.permissions.includes("media.delete") && <DeleteFile id={file.id} />}</div></div>)}<p className="muted">Showing the 15 most recent files.</p></div>}
    </div>
  </section>;
}
