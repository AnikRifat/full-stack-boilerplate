import { z } from "zod";

export const userSchema = z.object({
  id: z.number().int(), name: z.string(), email: z.string(),
  permissions: z.array(z.string()),
});
export const userResponseSchema = z.object({ data: userSchema });
export const authResponseSchema = z.object({ data: z.object({
  user: userSchema, token: z.string(), token_type: z.literal("Bearer"),
  expires_at: z.string().datetime({ offset: true }),
}) });
export const configurationSchema = z.object({ data: z.object({
  app_name: z.string(), support_email: z.string(), registration_enabled: z.boolean(),
}) });
export const mediaSchema = z.object({
  id: z.number().int(), filename: z.string(), collection: z.string(),
  mime_type: z.string(), size: z.number(), url: z.string().url(), created_at: z.string(),
});
export const mediaListSchema = z.object({ data: z.array(mediaSchema) });
export const mediaResponseSchema = z.object({ data: mediaSchema });
export type User = z.infer<typeof userSchema>;
export type FormState = { message?: string; errors?: Record<string, string[]>; success?: boolean };
