import "server-only";
import { z } from "zod";

const errorSchema = z.object({
  message: z.string().optional(), errors: z.record(z.string(), z.array(z.string())).optional(),
});
export class ApiError extends Error {
  constructor(public status: number, message: string, public fields: Record<string, string[]> = {}) {
    super(message);
  }
}

type Options = {
  method?: "GET" | "POST" | "PATCH" | "DELETE";
  token?: string;
  body?: Record<string, unknown> | FormData;
};
export async function apiRequest<S extends z.ZodType>(path: string, schema: S, options: Options = {}): Promise<z.output<S>> {
  const base = process.env.API_BASE_URL ?? "http://127.0.0.1:8000/api/v1";
  const headers = new Headers({ Accept: "application/json" });
  if (options.token) headers.set("Authorization", `Bearer ${options.token}`);
  const multipart = options.body instanceof FormData;
  if (options.body && !multipart) headers.set("Content-Type", "application/json");
  let response: Response;
  try {
    response = await fetch(`${base.replace(/\/$/, "")}${path}`, {
      method: options.method ?? "GET", headers,
      body: options.body instanceof FormData ? options.body : options.body ? JSON.stringify(options.body) : undefined,
      cache: "no-store", signal: AbortSignal.timeout(15_000),
    });
  } catch (error) {
    if (!(error instanceof TypeError) && !(error instanceof DOMException)) throw error;
    throw new ApiError(503, "The service is unavailable. Please try again shortly.");
  }
  const raw: unknown = response.status === 204 ? null : await response.json().catch(() => null);
  if (!response.ok) {
    const error = errorSchema.safeParse(raw);
    const message = response.status >= 500 ? "The service is unavailable. Please try again shortly."
      : response.status === 429 ? "Too many attempts. Please try again in a minute."
      : error.success ? error.data.message ?? "The request could not be completed." : "The request could not be completed.";
    throw new ApiError(response.status, message, error.success ? error.data.errors : {});
  }
  const result = schema.safeParse(raw);
  if (!result.success) throw new ApiError(502, "The service returned an unexpected response. Please try again later.");
  return result.data;
}
