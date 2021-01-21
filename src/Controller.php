<?php
    namespace miraccul\ticTacToe\Controller;
    use function miraccul\ticTacToe\View\showGame;

    function startGame()
    {
        echo "Game started...".PHP_EOL;
        showGame();
    }