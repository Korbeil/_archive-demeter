<?php
/**
 * Created by PhpStorm.
 * User: baptisteleduc
 * Date: 08/03/15
 * Time: 23:40
 */

    // data
    $array = Array(
        'corp'      => Array(
            '4333406', 'eum3QOOyms5N3CGY2iBGvegorFnJEmDQcQCJo8i2SH2qOBEk9OQbss1vYTqviAkd'
        ),
        'Engel44mp' => Array(
            Array('4181688', 'LVDmhBlTXbiJCf4F7zZBOusy31LKappbwbE39zM2QUwnlEhkztD4H36bQs8I07yz'),
            Array('4181691', 'PZ7cwVkK1vbVZddvLgMX1M2z4ZYe78eYtSXFFOuBvtjMfbFSeqAbaqgiDU9Y2mUl'),
            Array('4181696', 'pqtjWS4opkyfrGNjeF9qStVDTJKljlpU6NEtDgyGQ3AEmkw8xwqKk7hOHYUk2hiR'),
            Array('4181698', 'oLa70KzYUSEHg1oNCYpm916i4OaGRef4NJeEPpnIkGLawGbf13ymovZOxQfWE4du')
        ),
        'harlow_pi' => Array(
            Array('4335254', 'LGJGGwz4SKBEE0kU4EttoqZaway1gUqDJlp3XG8dFGY0MMjBMXpv7QjAaEM0c6aI'),
            Array('4335253', '5YILw2sNkVyTpOJTMiYzZX6rsbhielZnCgltwmlBj65SgF1se8OLxR9Zb5MP8DCS'),
            Array('4335256', 'kmjxyPakZzHAFxHtzRe8GfLvY8pn7Q4ZnPb0nyRp4N5fSsa1Ceror5QQUlWTrFDw'),
            Array('4335257', 'FpsbfPXq3siaASRmaK8nLSqe6ohoNxq3D9Q7yjprNLloccaG4S5YcyWa5cYOyX9s'),
            Array('4335259', 'lx5NMAhQkjlQjM1UWltHPrZaZKjE6KcRCH2Zp6m2afFNuXCAcXYlFiWFMsPXl9yD')
        )
    );

    $items_type = Array(
        // barren
        2524            => Array(
            'type'      => 'command-center',
            'planet'    => 'barren'
        ),
        2848            => Array(
            'type'      => 'extractor',
            'planet'    => 'barren'
        ),
        2473            => Array(
            'type'      => 'basic-industry',
            'planet'    => 'barren'
        ),
        2474            => Array(
            'type'      => 'advanced-industry',
            'planet'    => 'barren'
        ),
        2541            => Array(
            'type'      => 'storage',
            'planet'    => 'barren'
        ),
        2544            => Array(
            'type'      => 'launchpad',
            'planet'    => 'barren'
        ),

        // oceanic
        2525            => Array(
            'type'      => 'command-center',
            'planet'    => 'oceanic'
        ),
        3063            => Array(
            'type'      => 'extractor',
            'planet'    => 'oceanic'
        ),
        2490            => Array(
            'type'      => 'basic-industry',
            'planet'    => 'oceanic'
        ),
        2485            => Array(
            'type'      => 'advanced-industry',
            'planet'    => 'oceanic'
        ),
        2535            => Array(
            'type'      => 'storage',
            'planet'    => 'oceanic'
        ),
        2542            => Array(
            'type'      => 'launchpad',
            'planet'    => 'oceanic'
        ),

        // storm
        2550            => Array(
            'type'      => 'command-center',
            'planet'    => 'storm'
        ),
        3067            => Array(
            'type'      => 'extractor',
            'planet'    => 'storm'
        ),
        2483            => Array(
            'type'      => 'basic-industry',
            'planet'    => 'storm'
        ),
        2484            => Array(
            'type'      => 'advanced-industry',
            'planet'    => 'storm'
        ),
        2561            => Array(
            'type'      => 'storage',
            'planet'    => 'storm'
        ),
        2557            => Array(
            'type'      => 'launchpad',
            'planet'    => 'storm'
        ),

        // lava
        2549            => Array(
            'type'      => 'command-center',
            'planet'    => 'lava'
        ),
        3062            => Array(
            'type'      => 'extractor',
            'planet'    => 'lava'
        ),
        2469            => Array(
            'type'      => 'basic-industry',
            'planet'    => 'lava'
        ),
        2470            => Array(
            'type'      => 'advanced-industry',
            'planet'    => 'lava'
        ),
        2555            => Array(
            'type'      => 'launchpad',
            'planet'    => 'lava'
        )

    );

    $items_info = Array(
        /////////////////////////////////////////////////
        // Lava

        2306    => Array(
            'name'  => 'Non-CS Crystals',
            'tech'  => 0,
            'mass'  => 0.01,
            'color' => '#2f7ed8'
        ),
        2307    => Array(
            'name'  => 'Felsic Magma',
            'tech'  => 0,
            'mass'  => 0.01,
            'color' => '#8bbc21'
        ),
        2401    => Array(
            'name'  => 'Chiral Structures',
            'tech'  => 1,
            'mass'  => 0.38,
            'color' => '#910000'
        ),
        9828    => Array(
            'name'  => 'Silicon',
            'tech'  => 1,
            'mass'  => 0.38,
            'color' => '#1aadce'
        ),
        9842    => Array(
            'name'  => 'Miniatures Electronics',
            'tech'  => 2,
            'mass'  => 1.5,
            'color' => '#f28f43'
        ),

        /////////////////////////////////////////////////
        // Barren

        2267    => Array(
            'name'  => 'Base Metals',
            'tech'  => 0,
            'mass'  => 0.01,
            'color' => '#2f7ed8'
        ),
        2270    => Array(
            'name'  => 'Noble Metals',
            'tech'  => 0,
            'mass'  => 0.01,
            'color' => '#8bbc21'
        ),
        2398    => Array(
            'name'  => 'Reactive Metals',
            'tech'  => 1,
            'mass'  => 0.38,
            'color' => '#910000'
        ),
        2399    => Array(
            'name'  => 'Precious Metals',
            'tech'  => 1,
            'mass'  => 0.38,
            'color' => '#1aadce'
        ),
        3689    => Array(
            'name'  => 'Mechanical Parts',
            'tech'  => 2,
            'mass'  => 1.5,
            'color' => '#f28f43'
        )
    );

    $content_type = Array(
        3689    => 'T2',
        3775    => 'T2',
        9832    => 'T2'
    );