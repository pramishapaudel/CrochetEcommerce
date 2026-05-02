<?php
/**
 * Central place for DSA-style sorting and searching (Project II / report).
 * Include from pages: require_once __DIR__ . '/algorithms.php';
 *
 * Implemented here:
 * - Bubble sort  — O(n²) time, O(1) extra space (typical classroom algorithm)
 * - Quick sort   — average O(n log n) (used for shop listing / scores)
 * - Linear search — O(n) first-match scan
 * - Binary search — O(log n) on array sorted by a numeric key (e.g. productId)
 */

if (!function_exists('algo_bubble_sort')) {
    /**
     * Bubble sort. $compare($a, $b) returns int: <0 if a before b, 0 equal, >0 if a after b.
     *
     * @param array    $items
     * @param callable $compare function(mixed $a, mixed $b): int
     * @return array
     */
    function algo_bubble_sort(array $items, callable $compare): array
    {
        $arr = array_values($items);
        $n = count($arr);
        for ($i = 0; $i < $n; $i++) {
            $swapped = false;
            for ($j = 0; $j < $n - $i - 1; $j++) {
                if ($compare($arr[$j], $arr[$j + 1]) > 0) {
                    $tmp = $arr[$j];
                    $arr[$j] = $arr[$j + 1];
                    $arr[$j + 1] = $tmp;
                    $swapped = true;
                }
            }
            if (!$swapped) {
                break;
            }
        }
        return $arr;
    }
}

if (!function_exists('algo_quick_sort')) {
    /**
     * Quick sort (Lomuto-style partition by first element as pivot).
     * Average time O(n log n); worst case O(n²) if pivot is always extreme.
     *
     * @param array    $items
     * @param callable $compare function(mixed $a, mixed $b): int
     * @return array
     */
    function algo_quick_sort(array $items, callable $compare): array
    {
        $arr = array_values($items);
        $n = count($arr);
        if ($n < 2) {
            return $arr;
        }

        $pivot = $arr[0];
        $left = [];
        $right = [];
        for ($i = 1; $i < $n; $i++) {
            if ($compare($arr[$i], $pivot) < 0) {
                $left[] = $arr[$i];
            } else {
                $right[] = $arr[$i];
            }
        }

        return array_merge(
            algo_quick_sort($left, $compare),
            [$pivot],
            algo_quick_sort($right, $compare)
        );
    }
}

if (!function_exists('algo_sort_for_display')) {
    /**
     * Wrapper: use quick sort in production paths (faster on larger catalogs).
     * Switch to bubble sort for small demos if needed.
     *
     * @param array    $items
     * @param callable $compare
     * @param string   $mode 'quick' or 'bubble'
     * @return array
     */
    function algo_sort_for_display(array $items, callable $compare, string $mode = 'quick'): array
    {
        return $mode === 'bubble'
            ? algo_bubble_sort($items, $compare)
            : algo_quick_sort($items, $compare);
    }
}

if (!function_exists('algo_linear_search')) {
    /**
     * Linear search: first index where $predicate($item) is true.
     *
     * @param array    $items
     * @param callable $predicate function(mixed $item): bool
     * @return int index, or -1 if not found
     */
    function algo_linear_search(array $items, callable $predicate): int
    {
        foreach ($items as $index => $item) {
            if ($predicate($item)) {
                return (int) $index;
            }
        }
        return -1;
    }
}

if (!function_exists('algo_binary_search_by_int_key')) {
    /**
     * Binary search on $items sorted ascending by integer key from $keyFn($item).
     *
     * @param array    $items   sorted ascending by key
     * @param int      $target
     * @param callable $keyFn   function(mixed $item): int
     * @return int index, or -1 if not found
     */
    function algo_binary_search_by_int_key(array $items, int $target, callable $keyFn): int
    {
        $left = 0;
        $right = count($items) - 1;
        while ($left <= $right) {
            $mid = intdiv($left + $right, 2);
            $k = (int) $keyFn($items[$mid]);
            if ($k === $target) {
                return $mid;
            }
            if ($k < $target) {
                $left = $mid + 1;
            } else {
                $right = $mid - 1;
            }
        }
        return -1;
    }
}
