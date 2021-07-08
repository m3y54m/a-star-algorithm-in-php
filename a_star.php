<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>الگوریتم مسیریابی A*</title>
</head>
<body dir="rtl" style="font-family: Tahoma; font-size:9pt">
<?php    
    function generateRandomMap(&$myMap, $dimension = 10) {
        for ($j = 0 ; $j < $dimension ; $j++) {
            for ($i = 0 ; $i < $dimension ; $i++) {
                if ( ($i == 0) && ($j == 0) ) {
                    $myMap[$i][$j] = 3; /* Always the first square is the original square which we specify it by the number 3 */
                } else {
                    if ( ($i == $dimension - 1) && ($j == $dimension - 1) ) {
                        $myMap[$i][$j] = 4; /* Always the last square is the destination square which we specify it by the number 4 */
                    } else {
                        $rndNum = mt_rand() % 4;
                        switch ( $rndNum ) {
                            case 0:                  /* If the random number is 0 or 1 or 2 (it has triple chance), it is an open square which we specify it by the number 0 */
                            case 1:
                            case 2:
                                $myMap[$i][$j] = 0;
                                break;
                            case 3:                  /* If the random number is 1, it is an obstacle (wall, lake etc.) which we specify it by the number 2 */
                                $myMap[$i][$j] = 2;
                                break;
                        }
                    } /* end else */
                } /* end else */
            } /* end for */
        } /* end for */
    }
    function printMap($myMap, $dimension = 10) {
        

        echo '<table dir="ltr" border="1" cellspacing="0" cellpadding="0">';

        for ($j = 0 ; $j < $dimension ; $j++)
        {
            echo '<tr>';
            for ($i = 0 ; $i < $dimension ; $i++)
            {
                switch ( $myMap[$i][$j] )
                {
                case 0:                  /* If quantity of the current element of map array is 0, it represents an open square showed by a ' ' */
                    $c = '#FFFFFF';
                    break;
                case 1:                  /* If quantity of the current element of map array is 1, it represents a square of final path showed by a '1' */
                    $c = '#C0FFC0';
                    break;
                case 2:                  /* If quantity of the current element of map array is 2, it represents an obstacle (wall, lake etc.) showed by a '#' */
                    $c = '#585858';
                    break;
                case 3:                  /* If quantity of the current element of map array is 3, it represents the original square showed by '@' */
                    $c = '#FF0000';
                    break;
                case 4:                  /* If quantity of the current element of map array is 4, it represents the destination square showed by '$' */
                    $c = '#0000FF';
                    break;
                }
                echo '<td width="20" height="20" bgcolor="' . $c . '">&nbsp;</td>' ;
                if ($i <= $dimension - 2)
                    echo( " " );
            }
            echo '</tr>';
        }

        echo '</table>';
    }
    function findShortestWay($myMap, $dimension = 10, &$solvedMap) {      
        $isWay = 0; /* Indicates the result of path finding: 0 means there is no way, 1 means there is at least one way, 2 means the map is incomplete */

        $originalSquare['x'] = -1;      /* A sign that shows there is no original square on map */
        $destinationSquare['x'] = -1;   /* A sign that shows there is no destination square on map */

        /* Find the positions of original and destination squares (Begin:) */
        for ($i = 0 ; $i < $dimension ; $i++) {
            for ($j = 0 ; $j < $dimension ; $j++) {
                if ($myMap[$i][$j] == 3) {
                    $originalSquare['x'] = $i;
                    $originalSquare['y'] = $j;
                }
                if ($myMap[$i][$j] == 4) {
                    $destinationSquare['x'] = $i;
                    $destinationSquare['y'] = $j;
                }
            }
        }
        /* Find the positions of original and destination squares (End.) */

        if ( ($originalSquare['x'] == -1) || ($destinationSquare['x'] == -1) ) {
            $isWay = 2;
        } else {

           $tempG = 0;
            $T = 0; /* Counts items of Open-List */
            $N = 0; /* Counts items of Closed-List */
            $openList[0]['F'] = -1; /* A sign that shows Open-List is empty */

            do {
                if ($N == 0) {
                    /* Get the square with the lowest F score */
                    $currentSquare['x'] = $originalSquare['x'];
                    $currentSquare['y'] = $originalSquare['y'];

                    /* Add the current square to the Closed-List */
                    $closedList[0]['x'] = $currentSquare['x'];
                    $closedList[0]['y'] = $currentSquare['y'];
                    $closedList[0]['H'] = abs($destinationSquare['x'] - $originalSquare['x']) + abs($destinationSquare['y'] - $originalSquare['y']);
                    $closedList[0]['G'] = 0;
                    $closedList[0]['F'] = $closedList[0]['G'] + $closedList[0]['H'];
                    $N++;
                } else {
                    /* Get the square with the lowest F score (Begin:) */
                    $lowestF = $openList[$T - 1]['F']; /* The Square most recently  added to the Open-List */
                    $placeOfLowestF = $T - 1;
                    for ($m = $T - 2 ; $m >= 0 ; $m--)
                    {
                        if ($openList[$m]['F'] < $lowestF)
                        {
                            $lowestF = $openList[$m]['F'];
                            $placeOfLowestF = $m;
                        }
                    }

                    $currentSquare['x'] = $openList[$placeOfLowestF]['x'];
                    $currentSquare['y'] = $openList[$placeOfLowestF]['y'];
                    /* Get the square with the lowest F score (End.) */

                    /* Add the current square to the Closed-List */
                    $closedList[$N]['x'] = $currentSquare['x'];
                    $closedList[$N]['y'] = $currentSquare['y'];
                    $closedList[$N]['F'] = $openList[$placeOfLowestF]['F'];
                    $closedList[$N]['G'] = $openList[$placeOfLowestF]['G'];
                    $closedList[$N]['H'] = $openList[$placeOfLowestF]['H'];
                    $placeOfCurrentSquare = $N;
                    $N++;

                    /* Remove current square from the Open-List (Begin:) */
                    $tempG = $openList[$placeOfLowestF]['G']; /* Save G score of current square for future */

                    if ( $placeOfLowestF == ($T - 1) ) {
                        $openList[$T - 1]['G'] = -1; /* -1 represents empty cell on Open-List */
                        $openList[$T - 1]['H'] = -1;
                        $openList[$T - 1]['F'] = -1;
                        $openList[$T - 1]['x'] = -1;
                        $openList[$T - 1]['y'] = -1;

                        $T--;
                    } else {
                        for ($m = $placeOfLowestF ; $m <= ($T - 2) ; $m++) {
                            $openList[$m]['G'] = $openList[$m + 1]['G'];
                            $openList[$m]['H'] = $openList[$m + 1]['H'];
                            $openList[$m]['F'] = $openList[$m + 1]['F'];
                            $openList[$m]['x'] = $openList[$m + 1]['x'];
                            $openList[$m]['y'] = $openList[$m + 1]['y'];
                        }

                        $openList[$T - 1]['G'] = -1; /* -1 represents empty cell on Open-List */
                        $openList[$T - 1]['H'] = -1;
                        $openList[$T - 1]['F'] = -1;
                        $openList[$T - 1]['x'] = -1;
                        $openList[$T - 1]['y'] = -1;

                        $T--;
                    }
                    /* Remove current square from the Open-List (End.) */

                    /* If we added the destination to the Closed-List, we've found a path (Begin:) */
                    $isInClosedList = 0;
                    for ($m = 0 ; $m < $N ; $m++) {
                        if ( ($destinationSquare['x'] == $closedList[$m]['x']) &&
                                ($destinationSquare['y'] == $closedList[$m]['y']) ) {
                            $isInClosedList = 1;
                            break;
                        }
                    }
                    if ($isInClosedList == 1) {
                        /* PATH FOUND */
                        $isWay = 1;
                        break;  /* break the main loop */
                    }
                    /* If we added the destination to the Closed-List, we've found a path (End.) */
                } /* end else */

                /* Retrieve all its walkable adjacent squares (Begin:) */
                /* Top: */
                $adjacentSquares[0]['x'] = $currentSquare['x'];
                $adjacentSquares[0]['y'] = $currentSquare['y'] - 1;
                /* Left: */
                $adjacentSquares[1]['x'] = $currentSquare['x'] - 1;
                $adjacentSquares[1]['y'] = $currentSquare['y'];
                /* Bottom: */
                $adjacentSquares[2]['x'] = $currentSquare['x'];
                $adjacentSquares[2]['y'] = $currentSquare['y'] + 1;
                /* Right: */
                $adjacentSquares[3]['x'] = $currentSquare['x'] + 1;
                $adjacentSquares[3]['y'] = $currentSquare['y'];
                /* Retrieve all its walkable adjacent squares (End.) */

                for ($k = 0 ; $k <= 3 ; $k++)
                {
                    /* If this adjacent square is already in the Closed-List or if it is not an open square, ignore it (Begin:) */
                    $isInClosedList = 0;
                    for ($m = 0 ; $m < $N ; $m++) {
                        if ( ($adjacentSquares[$k]['x'] == $closedList[$m]['x']) &&
                                ($adjacentSquares[$k]['y'] == $closedList[$m]['y']) ) {
                            $isInClosedList = 1;
                            break;
                        }
                    }
                    if ( ($adjacentSquares[$k]['x'] < 0) ||
                            ($adjacentSquares[$k]['y'] < 0) ||
                            ($adjacentSquares[$k]['x'] >= $dimension) ||
                            ($adjacentSquares[$k]['y'] >= $dimension) ||
                            ($myMap[$adjacentSquares[$k]['x']][$adjacentSquares[$k]['y']] == 2) ||
                            ($isInClosedList == 1) ) {
                        continue; /* Go to the next adjacent square */
                    }
                    /* If this adjacent square is already in the Closed-List or if it is not an open square, ignore it (End.) */


                    $isInOpenList = 0;
                    for ($m = 0 ; $m < $T ; $m++) {
                        if ( ($adjacentSquares[$k]['x'] == $openList[$m]['x']) &&
                                ($adjacentSquares[$k]['y'] == $openList[$m]['y']) ) {
                            $isInOpenList = 1;
                            $temp = $m;
                            break;
                        }
                    }


                    if ($isInOpenList != 1) { /* If its not in the Open-List */
                        /* Compute its score and add it to the Open-List */
                        $openList[$T]['H'] = abs($destinationSquare['x'] - $adjacentSquares[$k]['x']) + abs($destinationSquare['y'] - $adjacentSquares[$k]['y']);;
                        $openList[$T]['G'] = $tempG + 1;
                        $openList[$T]['F'] = $openList[$T]['H'] + $openList[$T]['G'];
                        $openList[$T]['x'] = $adjacentSquares[$k]['x'];
                        $openList[$T]['y'] = $adjacentSquares[$k]['y'];
                        $T++;
                    } else { /* if its already in the Open-List */
                        if ( ($closedList[$placeOfCurrentSquare]['G'] + 1) < $openList[$temp]['G'] ) {
                            /* Update score of adjacent square that is in Open-List */
                            $openList[$temp]['G'] = $closedList[$placeOfCurrentSquare]['G'] + 1;
                            $openList[$temp]['F'] = $openList[$temp]['G'] + $openList[$temp]['H'];
                        }

                    }
                } /* end for*/
            }
            while ($openList[0]['F'] != -1);   /* Continue until there is no more available square in the Open-List (which means there is no path) */



            if ($isWay == 1)   /* If there is at least one way to the destination square*/
            {
                /* Now all the algorithm has to do is go backwards to figure out the final path! */
                $m = 0;
                $currentSquare['x'] = $closedList[$N - 1]['x'];
                $currentSquare['y'] = $closedList[$N - 1]['y'];
                $tempG = $closedList[$N - 1]['G'];
                do {
                    if ($m > 0) {
                        $finalPath[$m - 1]['x'] = $currentSquare['x'];
                        $finalPath[$m - 1]['y'] = $currentSquare['y'];
                    }

                    /* Retrieve all its walkable adjacent squares (Begin:) */
                    /* Top: */
                    $adjacentSquares[0]['x'] = $currentSquare['x'];
                    $adjacentSquares[0]['y'] = $currentSquare['y'] - 1;
                    /* Left: */
                    $adjacentSquares[1]['x'] = $currentSquare['x'] - 1;
                    $adjacentSquares[1]['y'] = $currentSquare['y'];
                    /* Bottom: */
                    $adjacentSquares[2]['x'] = $currentSquare['x'];
                    $adjacentSquares[2]['y'] = $currentSquare['y'] + 1;
                    /* Right: */
                    $adjacentSquares[3]['x'] = $currentSquare['x'] + 1;
                    $adjacentSquares[3]['y'] = $currentSquare['y'];
                    /* Retrieve all its walkable adjacent squares (End.) */
                    
                    for ($k = 0 ; $k <= 3 ; $k++)
                    {
                        /* If this adjacent square is not an open square, ignore it (Begin:) */
                        if ( ($adjacentSquares[$k]['x'] < 0) ||
                                ($adjacentSquares[$k]['y'] < 0) ||
                                ($adjacentSquares[$k]['x'] >= $dimension) ||
                                ($adjacentSquares[$k]['y'] >= $dimension) ||
                                ($myMap[$adjacentSquares[$k]['x']][$adjacentSquares[$k]['y']] == 2) ) {
                            continue; /* Go to the next adjacent square */
                        }
                        /* If this adjacent square is not an open square, ignore it (End.) */

                        $isInClosedList = 0;
                        for ($j = 0 ; $j < $N ; $j++) {
                            if ( ($adjacentSquares[$k]['x'] == $closedList[$j]['x']) &&
                                    ($adjacentSquares[$k]['y'] == $closedList[$j]['y']) ) {
                                $isInClosedList = 1;
                                $temp = $j;
                                break;
                            }
                        }

                        if ($isInClosedList == 1) { /* If this adjacent square is in the Closed-List */
                            if ( $closedList[$temp]['G'] == ($tempG - 1) ) {
                                $m++;
                                $currentSquare['x'] = $closedList[$temp]['x'];
                                $currentSquare['y'] = $closedList[$temp]['y'];
                                $tempG = $closedList[$temp]['G'];
                                break;

                            }
                        }
                    } /* end for*/
                }
                while ($tempG != 0);

                /* Copy MapArray to SolvedMapArray (Begin:)*/
                for ($i = 0 ; $i < $dimension ; $i++) {
                    for ($j = 0 ; $j < $dimension ; $j++) {
                        $solvedMap[$i][$j] = $myMap[$i][$j];
                    }
                }
                /* Copy MapArray to SolvedMapArray (End.)*/

                /* Write FinalPath on the SolvedMapArray (Begin.)*/
                for ($i = 0 ; $i <= ($m - 2) ; $i++) {
                    $solvedMap[$finalPath[$i]['x']][$finalPath[$i]['y']] = 1;
                }
                /* Write FinalPath on the SolvedMapArray (End.)*/
            } /* end if*/
        } /* end else*/
        return $isWay;
    }
    
    $myMap = array();
    $solvedMap = array();
    
    generateRandomMap($myMap, 10);
    printMap($myMap, 10);
    
    switch ( findShortestWay($myMap, 10, $solvedMap) ){
        case 0:
            echo '<br/>هیچ راهی وجود ندارد!<br/>';
            break;
        case 1:
            echo '<br/>این کوتاه ترین مسیر است<br/>';
            printMap($solvedMap, 10);
            echo '<br/>شما رسیدید<br/>';
            break;
        case 2:
            echo '<br/>خطا: نقشه ناقص است!<br/>';
            break;
        } /* end switch*/
?>

</body>
</html>
