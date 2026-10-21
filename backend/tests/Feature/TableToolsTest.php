<?php

namespace Tests\Feature;

use App\Livewire\Admin\Users\Index as UserIndex;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use OpenSpout\Reader\XLSX\Reader;
use Tests\TestCase;

class TableToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_excel_export_follows_the_list_filters_and_picked_columns(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'name' => 'Root Owner']);
        User::factory()->create(['name' => 'Karim Hasan', 'email' => 'karim@example.test']);
        User::factory()->create(['name' => 'Rahim Uddin', 'email' => 'rahim@example.test']);
        $this->actingAs($owner);

        $rows = $this->export(Livewire::test(UserIndex::class)->set('search', 'karim'), 'all', ['name', 'email']);

        $this->assertSame(__('Users'), $rows[0][0]);
        $this->assertContains([__('Name'), __('Email address')], $rows);
        $this->assertContains(['Karim Hasan', 'karim@example.test'], $rows);
        $this->assertStringNotContainsString('Rahim Uddin', json_encode($rows));
        $this->assertStringNotContainsString('Root Owner', json_encode($rows));
    }

    public function test_selected_rows_only_narrow_the_visible_rows(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $karim = User::factory()->create(['name' => 'Karim Hasan']);
        User::factory()->create(['name' => 'Rahim Uddin']);
        $employee = Employee::factory()->create();

        $component = Livewire::test(UserIndex::class, ['employee' => true])->set('selected', [(string) $karim->id, (string) $employee->user_id]);
        $names = array_column($this->bodyRows($this->export($component, 'selected', ['name']), __('Name')), 0);

        $this->assertSame([$employee->user->name], $names);
    }

    public function test_employee_exports_carry_the_staff_columns(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        Employee::factory()->create(['employee_code' => 'EMP-042', 'department' => 'Support']);

        $rows = $this->export(Livewire::test(UserIndex::class, ['employee' => true]), 'all', ['code', 'department']);

        $this->assertContains([__('Employee code'), __('Department')], $rows);
        $this->assertContains(['EMP-042', 'Support'], $rows);
    }

    public function test_a_print_page_is_shown_only_to_the_user_who_prepared_it(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        User::factory()->create(['name' => 'Karim Hasan', 'email' => 'karim@example.test']);
        $this->actingAs($owner);

        $url = Livewire::test(UserIndex::class)->call('exportTable', 'print', 'all', ['name'], 'landscape')->effects['dispatches'][0]['params']['url'];
        $this->get($url)->assertOk()->assertSee('Karim Hasan')->assertDontSee('karim@example.test')->assertSee('A4 landscape');

        $this->actingAs(User::factory()->create(['role' => 'administrator']))->get($url)->assertNotFound();
        Cache::flush();
        $this->actingAs($owner)->get($url)->assertNotFound();
    }

    /**
     * @param  'all'|'selected'  $scope
     * @param  list<string>  $columns
     * @return list<list<mixed>> the rows of the downloaded workbook's first sheet
     */
    private function export(mixed $component, string $scope, array $columns): array
    {
        $download = $component->call('exportTable', 'xlsx', $scope, $columns)->effects['download'];

        return $this->readXlsx(base64_decode($download['content']));
    }

    /** @return list<list<mixed>> the rows of the first sheet of an .xlsx file */
    private function readXlsx(string $contents): array
    {
        $path = tempnam(sys_get_temp_dir(), 'test-xlsx');
        file_put_contents($path, $contents);
        $reader = new Reader;
        $reader->open($path);
        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
            }
            break;
        }
        $reader->close();
        unlink($path);

        return $rows;
    }

    /**
     * @param  list<list<mixed>>  $rows
     * @return list<list<mixed>> the rows after the header row that starts with $firstLabel
     */
    private function bodyRows(array $rows, string $firstLabel): array
    {
        $header = array_search($firstLabel, array_column($rows, 0), true);
        $this->assertNotFalse($header, 'The export has no header row.');

        return array_slice($rows, $header + 1);
    }
}
