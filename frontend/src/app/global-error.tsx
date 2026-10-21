"use client";

import "./globals.css";

export default function GlobalError({ reset }: { reset: () => void }) {
  return <html lang="en"><body><main className="container"><div className="auth-card panel stack"><h1>Temporarily unavailable</h1><p className="muted">We could not load the application. Please try again shortly.</p><button className="btn" onClick={reset}>Try again</button></div></main></body></html>;
}
