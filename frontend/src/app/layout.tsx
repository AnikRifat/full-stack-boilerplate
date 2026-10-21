import type { Metadata } from "next";
import Link from "next/link";
import { connection } from "next/server";
import { apiRequest } from "@/lib/api";
import { configurationSchema } from "@/lib/contracts";
import "./globals.css";

export const metadata: Metadata = { title: { default: "Starter", template: "%s · Starter" }, description: "A reusable application foundation." };
export default async function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  await connection();
  const { data: settings } = await apiRequest("/configuration", configurationSchema);
  return <html lang="en"><body><a className="sr-only focus:not-sr-only" href="#main">Skip to content</a>
    <header className="site-header"><div className="container header-inner"><Link className="brand" href="/"><span className="brand-mark">S</span>{settings.app_name}</Link><nav className="nav" aria-label="Main navigation"><Link href="/">Home</Link><Link href="/account">Account</Link><Link href="/login">Sign in</Link></nav></div></header>
    <main id="main" className="container">{children}</main><footer className="footer"><div className="container muted">A clean foundation for your next application. {settings.support_email && <a className="text-link" href={`mailto:${settings.support_email}`}>Contact support</a>}</div></footer>
  </body></html>;
}
