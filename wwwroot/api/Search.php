<?php
include ('common.php');
include('BZSearch.php');

class Search {
    public static function SearchContext($args) {
        $data = BZSearch::SearchContext($args['terms']);
        return outSuccess($data,count($data));
    }
}
?>