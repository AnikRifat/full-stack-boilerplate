"use client";

export default function ErrorPage({ reset }: { reset: () => void }) {
  return <div className="auth-card panel stack"><h1>We could not load this page</h1><p className="muted">The service may be temporarily unavailable. Please try again.</p><button className="btn" onClick={reset}>Try again</button></div>;
}
