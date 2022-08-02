<?php
namespace App\Services\Pagination;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class Paginator
{
    /**
     * All of the items being paginated.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $items;

    /**
     * The number of items to be shown per page.
     *
     * @var int
     */
    protected $perPage;

    /**
     * The total number of items before slicing.
     *
     * @var int
     */
    protected $total;

    /**
     * The base path to assign to all URLs.
     *
     * @var string
     */
    protected $path = '/';


    /**
     * Merge paginator instances
     *
     * @param  mixed $paginators
     * @param  bool  $descending
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    function merge($paginators)
    {
        $paginators = is_array($paginators) ? $paginators : func_get_args();

        foreach ($paginators as $paginator) {
            if (!$paginator instanceof LengthAwarePaginator) {
                throw new InvalidArgumentException("Only LengthAwarePaginator may be merged.");
            }
        }

        $total   = array_reduce($paginators, function($carry, $paginator) {

            return $paginator->total();
        }, 0);

        $perPage = array_reduce($paginators, function($carry, $paginator) {

            return $paginator->perPage();
        }, 0);

        $items   = array_map(function($paginator) {

            return $paginator->items();

        }, $paginators);

        $items         = Arr::flatten($items);

        $items         = Collection::make($items);

        $this->items   = $items;
        $this->perPage = $perPage;
        $this->total   = $total;

        return $this;
    }

    /**
     * Sort the collection using the given callback.
     *
     * @param  callable|string  $callback
     * @param  int  $options
     * @param  bool  $descending
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
    {
        $this->items = $this->items->sortBy($callback, $options, $descending);

        return $this;
    }

    /**
     * Sort the collection in descending order using the given callback.
     *
     * @param  callable|string  $callback
     * @param  int  $options
     * @return static
     */
    public function sortByDesc($callback, $options = SORT_REGULAR)
    {
        return $this->sortBy($callback, $options, true);
    }

    /**
     * Get paginator
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function get()
    {
        return new LengthAwarePaginator(
            $this->items,
            $this->total,
            $this->perPage,
            LengthAwarePaginator::resolveCurrentPage(),
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
            ]
        );
    }
}
