# Central media storage

`backend/app/Services/MediaService.php` is the only permanent file storage boundary. Admin components, API controllers, and model helpers delegate to it. Feature code must not call `Storage::`, `$file->store()`, or delete physical files directly.

The `media` table records UUID, uploader, optional polymorphic owner, collection, disk, path, original filename, verified MIME type, bytes, image dimensions, timestamps, and soft deletion history. Physical names are UUID-based, not client-controlled paths. Collection names accept letters, numbers, underscores and hyphens only.

Images (JPEG, PNG, WebP, GIF), PDF, plain text and CSV are accepted up to 8 MB. SVG and executable uploads are rejected. Validation runs inside the shared service as well as at the HTTP/Livewire boundaries. Livewire temporary uploads use a local staging disk; successful permanent uploads always use the selected central disk.

```php
$media = app(\App\Services\MediaService::class)->store($file, $actor, 'document', $model);
$replacement = app(\App\Services\MediaService::class)->replace($media, $file, $actor);
app(\App\Services\MediaService::class)->delete($media, $actor);
```

Models using `App\Concerns\HasMedia` can call:

```php
$model->addMedia($file, $actor, 'avatar');
$model->getMedia('avatar');
$model->getFirstMediaUrl('avatar');
$model->clearMedia($actor, 'avatar');
```

Commit an owning model before storing its files. Do not wrap physical storage operations in a larger database transaction that may later roll back.

## Local and S3-compatible configuration

```dotenv
# Private local files
FILESYSTEM_DISK=local
MEDIA_DISK=local
```

```dotenv
# AWS S3 or a compatible provider
MEDIA_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_ENDPOINT=
AWS_URL=
AWS_USE_PATH_STYLE_ENDPOINT=false
```

AWS normally leaves `AWS_ENDPOINT` empty. Compatible services use their provider endpoint and required region/path-style values. The Flysystem S3 adapter is installed. Keep buckets private. Clear cached config after changing environment values (`php artisan config:clear` locally; rebuild config cache for your deployment).

Changing the default disk affects new uploads. Old media uses its recorded disk. Keep old disk configurations available. Changing the bucket or endpoint behind the same `s3` alias changes what that alias refers to; migrate existing objects or add a distinct disk alias instead. This starter does not silently move files.

Clients receive a five-minute signed Laravel download URL, not an unrestricted object path or permanent bucket URL. Treat signed links as temporary bearer credentials: they can be opened by anyone who has the link until expiry. A deleted file is unavailable even if its old signed link has not expired. Bytes are streamed through the Laravel service, using attachment disposition for documents and inline disposition for supported images.

Users list/read/delete their own files. `media.manage` additionally allows access across users; upload/delete permissions and token scopes still apply. The service cleans up a just-written file if tracking creation fails. Deletion removes bytes before soft-deleting the tracking row; failed physical deletion retains the active record. Replacement creates and tracks the new file before deleting the old one, retaining history.

Real S3/provider credentials are not included. Disk selection and ownership have been tested with isolated fake disks, plus actual local uploads through both UIs. Live provider connectivity must be checked in the target environment.
