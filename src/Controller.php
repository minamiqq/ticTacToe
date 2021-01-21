<?php

namespace miraccul\ticTacToe\Controller;

    use miraccul\ticTacToe\Model\Board as Board;
    use Exception as Exception;
    use LogicException as LogicException;

    use function miraccul\ticTacToe\View\showGameBoard;
    use function miraccul\ticTacToe\View\showMessage;
    use function miraccul\ticTacToe\View\getValue;

    use const miraccul\ticTacToe\Model\PLAYER_X_MARKUP;
    use const miraccul\ticTacToe\Model\PLAYER_O_MARKUP;

function startGame()
{
    $canContinue = true;
    do {
        $gameBoard = new Board();
        initialize($gameBoard);
        gameLoop($gameBoard);
        inviteToContinue($canContinue);
    } while ($canContinue);
}

function initialize($board)
{
    try {
        $board->setDimension(getValue("Enter the field size "));
        $board->initialize();
    } catch (Exception $e) {
        showMessage($e->getMessage());
        initialize($board);
    }
}

function gameLoop($board)
{
    $stopGame = false;
    $currentMarkup = PLAYER_X_MARKUP;
    $endGameMsg = "";

    do {
        showGameBoard($board);
        if ($currentMarkup == $board->getUserMarkup()) {
            processUserTurn($board, $currentMarkup, $stopGame);
            $endGameMsg = "Player '$currentMarkup' won!";
            $currentMarkup = $board->getComputerMarkup();
        } else {
            processComputerTurn($board, $currentMarkup, $stopGame);
            $endGameMsg = "Player '$currentMarkup' won!";
            $currentMarkup = $board->getUserMarkup();
        }

        if (!$board->isFreeSpaceEnough() && !$stopGame) {
            showGameBoard($board);
            $endGameMsg = "Draw!";
            $stopGame = true;
        }
    } while (!$stopGame);

    showGameBoard($board);
    showMessage($endGameMsg);
}

function processUserTurn($board, $markup, &$stopGame)
{
    $answerTaked = false;
    do {
        try {
            $coords = getCoords($board);
            $board->setMarkupOnBoard($coords[0], $coords[1], $markup);
            if ($board->determineWinner($coords[0], $coords[1]) !== "") {
                $stopGame = true;
            }

            $answerTaked = true;
        } catch (Exception $e) {
            showMessage($e->getMessage());
        }
    } while (!$answerTaked);
}

function getCoords($board)
{
    $markup = $board->getUserMarkup();
    $coords = getValue("Enter the coordinates for the player '$markup' through '-'. Format (Y, X).");
    $coords = explode("-", $coords);
    if (!isset($coords[1])) {
        throw new Exception("No second coordinate. Please try again.");
    }
    return $coords;
}

function processComputerTurn($board, $markup, &$stopGame)
{
    $answerTaked = false;
    do {
        $i = rand(1, $board->getDimension());
        $j = rand(1, $board->getDimension());
        try {
            $board->setMarkupOnBoard($i, $j, $markup);
            if ($board->determineWinner($i, $j) !== "") {
                $stopGame = true;
            }

            $answerTaked = true;
        } catch (Exception $e) {
        }
    } while (!$answerTaked);
}

function inviteToContinue(&$canContinue)
{
    $answer = "";
    do {
        $answer = getValue("Do you want to continue? (y/n)");
        if ($answer === "y") {
            $canContinue = true;
        } elseif ($answer === "n") {
            $canContinue = false;
        }
    } while ($answer !== "y" && $answer !== "n");
}
