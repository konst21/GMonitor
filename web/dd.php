<?php
/*
* Created by Kostyantyn Kyyashko
* k.konstantin.n@gmail.com
*/
/*
 * основа решения
 * 1) направления углов совпадают с нормальной ориентацией декартовой системы координат
 * 2) формат данных избыточен. Так, start можно заменить на turn, если считать начальный угол равным нулю
 * 3) указание кол-ва измерения также избыточно - оно равно числу строк, следующих за ним до следующего
 * указания числа измерений.
 * 4) формат данных очень жесткий: "<координаты> угол расстояние угол расстояние", что позволяет убрать turn и walk
 * из строк, представляющие в данном случае просто комментарии к данным
 *
 * Очень заманчиво было сделать что-то вроде "процессора", выполняющего команды "turn" и "walk",
 * но в данном случае, опираясь на пп.1-4 легко можно обойтись простыми операциями по работе со строками и массивами.
 *
 * Конечные точки вычисляем сложением векторов перемещений, которое сводится к сложению соотв. координат,
 * используя простую тригонометрию
 *
 * Нужно отметить, что пример вывода в задаче - не совсем корректный, так как в задаче указана точность 2 знака после
 * запятой, в примере явно больше, ну вот "97.1547 40.2334 7.63097"
 *
 */

$data = <<<text
3
87.342 34.30 start 0 walk 10.0
2.6762 75.2811 start -45.0 walk 40 turn 40.0 walk 60
58.518 93.508 start 270 walk 50 turn 90 walk 40 turn 13 walk 5
2
30 40 start 90 walk 5
40 50 start 180 walk 10 turn 90 walk 5
0
text;

/**
 * main logic and output
 */
$string_array = string_split($data);
$groups = create_groups($string_array);
$out = [];
$counter = 0;
foreach ($groups as $g) {
    $points = points($g);
    $out[$counter] = [$points['avg']['x'], $points['avg']['y'], wrong_way($points)];
    $counter++;
}

foreach ($out as $o) {
    echo implode(' ', $o) . '<br>';
}


/**
 * Simpy split lines by EOL
 * @param $data
 * @return array
 */
function string_split ($data)
{
    return explode(PHP_EOL, $data);
}

/**
 * Create test groups as array of string. Measure number placed as separate line, strings after that is a measure group
 * @param $string_array
 * @return array
 */
function create_groups ($string_array)
{
    $group_counter = -1;
    $out = [];

    foreach ($string_array as $data_string) {
        if (strlen($data_string) < 3) {
            $group_counter++;
        }
        else {
            $data_string = preg_replace('~[^\d^\.^\s\-]~', '' ,$data_string);
            $data_string = preg_replace('~\s{1,10}~', '|' ,trim($data_string));
            $out[$group_counter][] = trim($data_string);
        }
    }
    return $out;
}

/**
 * Calculate end points and average point of group
 * @param $group
 * @return array
 */
function points ($group)
{
    $x_avg = 0;
    $y_avg = 0;
    foreach ($group as $g) {
        $data = explode('|', $g);
        $x = $data[0];
        $y = $data[1];
        $angle = 0;
        unset($data[0]);
        unset($data[1]);
        $data_lengt = count($data)/2;
        for ($i = 0; $i <= $data_lengt; $i++) {
            $angle += $data[$i*2];
            $angle_rad = deg2rad($angle);
            $x += $data[$i*2+1]*cos($angle_rad);
            $y += $data[$i*2+1]*sin($angle_rad);
        }
        $x_avg += $x;
        $y_avg += $y;
        $out[] = [
            'x' => $x,
            'y' => $y,
        ];
    }
    $walk_count = count($group);
    $avg = [
        'x' => round($x_avg/$walk_count ,2),
        'y' => round($y_avg/$walk_count, 2)
    ];
    $out['avg'] = $avg;
    return $out;
}

/**
 * Calculate distance between average point and each group point, and define wrong way ))
 * @param $points
 * @return mixed
 */
function wrong_way($points)
{
    $x_avg = $points['avg']['x'];
    $y_avg = $points['avg']['y'];
    unset($points['avg']);
    $distance = [];
    foreach ($points as $p) {
        $distance[] = round(sqrt(pow(($x_avg - $p['x']), 2) + pow(($y_avg - $p['y']), 2)), 2);
    }
    return max($distance);
}

