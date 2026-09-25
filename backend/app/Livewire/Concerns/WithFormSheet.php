<?php

namespace App\Livewire\Concerns;

use Livewire\Attributes\Url;

/**
 * Create and edit forms open in an off-canvas sheet over their list instead of a separate page.
 * The sheet lives in the URL (?sheet=create, ?sheet=edit:12) so any page can
 * link straight into it. The form component inside keeps every authorization re-check.
 */
trait WithFormSheet
{
    #[Url(as: 'sheet', except: '')]
    public string $sheet = '';

    public function openSheet(string $sheet): void
    {
        $this->sheet = $sheet;
    }

    public function closeSheet(): void
    {
        $this->sheet = '';
    }

    /** The part before the colon: "create", "edit"… */
    public function sheetAction(): string
    {
        return strstr($this->sheet, ':', true) ?: $this->sheet;
    }

    /** The part after the colon: usually a record id or key. */
    public function sheetArgument(): string
    {
        return (string) substr((string) strstr($this->sheet, ':'), 1);
    }
}
