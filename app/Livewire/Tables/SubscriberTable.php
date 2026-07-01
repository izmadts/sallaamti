<?php

namespace App\Livewire\Tables;

use App\Models\Subscriber;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Livewire\Attributes\On;

final class SubscriberTable extends PowerGridComponent
{
    public string $tableName = 'subscribers-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Subscriber::query();
    }
    public function relationSearch(): array
    {
        return [];
    }
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()

            ->add('id')
            ->add('email')
            ->add('status', function (Subscriber $subscriber) {

                if (!$subscriber->is_active) {
                    return 'Inactive';
                }

                return $subscriber->verified_at ? 'Verified' : 'Pending';
            })

            ->add(
                'created_at_formatted',
                fn($subscriber) =>
                $subscriber->created_at->format('d M Y H:i')
            );
    }

    public function columns(): array
    {
        return [

            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Email', 'email')
                ->searchable()
                ->sortable(),

            Column::make('Status', 'status')
                ->sortable(),

            Column::make('Subscribed', 'created_at_formatted', 'created_at')
                ->sortable(),
                
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),          

            Column::action('Actions'),

        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[On('verifySubscriber')]
    public function verifySubscriber($id): void
    {
        Subscriber::findOrFail($id)->update([
            'verified_at' => now(),
            'is_active'   => true,
        ]);

        $this->dispatch('$refresh');
    }

    #[On('deleteSubscriber')]
    public function deleteSubscriber($id): void
    {
        Subscriber::findOrFail($id)->delete();

        $this->dispatch('$refresh');
    }
    public function actions(Subscriber $row): array
    {
        return [

            Button::add('verify')
                ->slot('Verify')
                ->class('bg-green-600 text-white px-2 py-1 rounded text-xs')
                ->dispatch('verifySubscriber', ['id' => $row->id]),

            Button::add('delete')
                ->slot('Delete')
                ->class('bg-red-600 text-white px-2 py-1 rounded text-xs')
                ->dispatch('deleteSubscriber', ['id' => $row->id]),

        ];
    }
}