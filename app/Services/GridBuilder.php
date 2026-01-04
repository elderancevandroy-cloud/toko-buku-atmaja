<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class GridBuilder
{
    protected $model;
    protected $columns = [];
    protected $actions = [];
    protected $searchable = [];
    protected $perPage = 10;
    protected $query;

    /**
     * Set the model for the grid
     */
    public function setModel($model)
    {
        $this->model = $model;
        $this->query = $model::query();
        return $this;
    }

    /**
     * Add a column to the grid
     */
    public function addColumn($key, $label, $sortable = true)
    {
        $this->columns[] = [
            'key' => $key,
            'label' => $label,
            'sortable' => $sortable
        ];
        return $this;
    }

    /**
     * Add an action button to the grid
     */
    public function addAction($label, $route, $class = 'btn-primary', $icon = null)
    {
        $this->actions[] = [
            'label' => $label,
            'route' => $route,
            'class' => $class,
            'icon' => $icon
        ];
        return $this;
    }

    /**
     * Set searchable columns
     */
    public function setSearchable($columns)
    {
        $this->searchable = is_array($columns) ? $columns : [$columns];
        return $this;
    }

    /**
     * Set items per page
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * Apply search filter to query
     */
    protected function applySearch($search)
    {
        if (!empty($search) && !empty($this->searchable)) {
            $this->query->where(function ($query) use ($search) {
                foreach ($this->searchable as $column) {
                    $query->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }
        return $this;
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting($sortBy, $sortDirection = 'asc')
    {
        if (!empty($sortBy)) {
            // Validate sort column exists in columns
            $columnKeys = array_column($this->columns, 'key');
            if (in_array($sortBy, $columnKeys)) {
                $this->query->orderBy($sortBy, $sortDirection);
            }
        }
        return $this;
    }

    /**
     * Get paginated data for the grid
     */
    public function getData(Request $request = null)
    {
        if ($request) {
            $search = $request->get('search');
            $sortBy = $request->get('sort_by');
            $sortDirection = $request->get('sort_direction', 'asc');
            $perPage = $request->get('per_page', $this->perPage);

            $this->applySearch($search);
            $this->applySorting($sortBy, $sortDirection);
            
            return $this->query->paginate($perPage);
        }

        return $this->query->paginate($this->perPage);
    }

    /**
     * Get JSON data for AJAX loading
     */
    public function getJsonData(Request $request)
    {
        try {
            $data = $this->getData($request);
            
            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ],
                'columns' => $this->columns,
                'actions' => $this->actions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Render the grid view
     */
    public function render($view = 'components.grid', $data = [])
    {
        $gridData = $this->getData();
        
        return view($view, array_merge([
            'data' => $gridData,
            'columns' => $this->columns,
            'actions' => $this->actions,
            'searchable' => !empty($this->searchable)
        ], $data));
    }

    /**
     * Get columns configuration
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get actions configuration
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Check if grid has searchable columns
     */
    public function isSearchable()
    {
        return !empty($this->searchable);
    }
}