<?php
echo "Ahmed Moshet PHP Loops HW <br>";

$x = array(5, 12, 35, 70, 300);

echo " <br> The even numbers are: <br> ";

for($i = 0; $i <count($x); $i++)
{
        if($x[$i] % 2 == 0) //if the array # is even (remainder of 0 when /2, print it)
    {
        echo " <br> $x[$i]";
    }
}
?>
