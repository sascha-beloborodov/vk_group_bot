<?php

namespace App\DataTables;

use App\Models\FAQ;
//use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;

class FAQDataTables extends DataTables
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable->addColumn('action', 'templates.f_a_q_s.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\FAQ $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(FAQ $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '80px'])
            ->parameters([
                'dom'     => 'Bfrtip',
                'order'   => [[0, 'desc']],
                'buttons' => [
                    'create',
                    'export',
                    'print',
                    'reset',
                    'reload',
                ],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'question',
            'answer',
            'keywords',
            'order',
            'is_active',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'f_a_q_sdatatable_' . time();
    }
}