import Link from "next/link";

export default function Home() {
  return <section className="hero"><p className="eyebrow">Your application starts here</p><h1>A simple start.<br />Room to make it yours.</h1><p className="lead">A clear workspace for your team and a connected experience for your users. Ready for the project you have in mind.</p><div className="flex flex-wrap gap-3"><Link className="btn" href="/register">Create an account</Link><Link className="btn btn-secondary" href="/login">Sign in</Link></div></section>;
}
