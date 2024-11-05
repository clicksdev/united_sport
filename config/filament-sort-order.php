<?php

// config for IbrahimBougaoua/FilamentSortOrder
return [

    /** Add the tables to be migrated */
    'tables' => [
        'sponsors',
        'advertisements',
    ],

    /* The column name to be used for sorting */
    'sort_column_name' => 'sort_order',

    /* Sort Order asc or desc */
    'sort' => 'asc',
];
