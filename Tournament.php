<?php

class Element
{
    private string $elementName;
    private array $weaknesses = [];

    public function __construct(string $elementName)
    {
        $this->elementName = $elementName;
    }

    public function getElementName(): string
    {
        return $this->elementName;
    }

    public function getWeaknesses(): array
    {
        return $this->weaknesses;
    }

    public function addWeakness(Element $element): void
    {
        $this->weaknesses[] = $element;
    }

    public function addWeaknesses(array $elements): void
    {
        foreach ($elements as $element) {

            if (!$element instanceof Element) continue;

            $this->addWeakness($element);
        }
    }

    public function isWeakAgainst(Element $element): bool
    {
        return in_array($element, $this->weaknesses);
    }

    public function displayElements(): void
    {
        foreach ($this->elements as $key => $element) {
            echo "[{$key}] - {$element->getName()}" . "\n";
        }
    }
}

$paper = new Element('Paper');
$rock = new Element('Rock');
$scissors = new Element('Scissors');

$rock->addWeaknesses([$paper]);
$paper->addWeaknesses([$scissors]);
$scissors->addWeaknesses([$rock]);

class Player
{
    private string $name;
    private ?Element $selection = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSelection(): Element
    {
        return $this->selection;
    }

    public function setSelection(Element $selection): void
    {
        $this->selection = $selection;
    }
}

class Game
{
    /**@var Element[] */
    private array $elements = [];

    private Player $attacker;
    private Player $defender;

    private ?Player $winner = null;

    public function __construct(Player $attacker, Player $defender)
    {
        $this->attacker = $attacker;
        $this->defender = $defender;

        $this->setup();
    }

    private function setup(): void
    {
        $this->elements = [
            $rock = new Element('Rock'),
            $paper = new Element('Paper'),
            $scissors = new Element('Scissors'),
        ];

        $rock->addWeaknesses([$paper]);
        $paper->addWeaknesses([$scissors]);
        $scissors->addWeaknesses([$rock]);
    }

    public function determineResult(): void
    {
        $this->attackerElement = $this->attacker->getSelection();
        $this->defenderElement = $this->defender->getSelection();

        if ($this->attacker->getSelection() === $this->defender->getSelection()) {
            return;
        }

        if ($this->attacker->getSelection()->isWeakAgainst($this->defender->getSelection())) {
            $this->winner = $this->defender;
            return;
        }
        $this->winner = $this->attacker;
    }

    public function getWinner(): ?Player
    {
        return $this->winner;
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function isTied(): bool
    {
        return is_null($this->winner); 
    }
}

/*
Lai spēlētu bez turnīra:

$player1 = new Player('Player 1');
$player2 = new Player('Player 2');

$game = new Game($player1, $player2);

$player1->setSelection($game->getElements()[0]);
$player2->setSelection($game->getElements()[1]);

$game->determineResult();

var_dump($game->getWinner()->getName());
*/

class GameSet
{
    private Player $attacker;
    private Player $defender;

    private Player $winner;

    private const MAX_WINS = 2;

    private int $attackerPoints = 0;
    private int $defenderPoints = 0;

    public function __construct(Player $attacker, Player $defender)
    {
        $this->attacker = $attacker;
        $this->defender = $defender;
    }

    public function determineResult(): void
    {
        while ($this->attackerPoints < self::MAX_WINS && $this->defenderPoints < self::MAX_WINS)
        {
            $game = new Game($this->attacker, $this->defender);

            $elements = $game->getElements();

            $attackerSelectedElementIndex = array_rand($elements);
            $defenderSelectedElementIndex = array_rand($elements);

            $this->attacker->setSelection($elements[$attackerSelectedElementIndex]);
            $this->defender->setSelection($elements[$defenderSelectedElementIndex]);

            $game->determineResult();

            if ($game->isTied()) continue;

            if ($game->getWinner() === $this->attacker) {
                $this->attackerPoints++;
            }

            if ($game->getWinner() === $this->defender) {
                $this->defenderPoints++;
            }
        }

        if ($this->attackerPoints > $this->defenderPoints) {
            $this->winner = $this->attacker;
            return;
        }
        $this->winner = $this->defender;
    }

    public function getWinner(): Player
    {
        return $this->winner;
    }

    public function getAttacker(): Player
    {
        return $this->attacker;
    }

    public function getDefender(): Player
    {
        return $this->defender;
    }

    public function getAttackerPoints(): int
    {
        return $this->attackerPoints;
    }

    public function getDefenderPoints(): int
    {
        return $this->defenderPoints;
    }
}

$game1 = new GameSet(new Player('P1'), new Player('P2'));
$game1->determineResult();

$game2 = new GameSet(new Player('P3'), new Player('P4'));
$game1->determineResult();

$game3 = new GameSet(new Player('P5'), new Player('P6'));
$game1->determineResult();

$game4 = new GameSet(new Player('P7'), new Player('P8'));
$game1->determineResult();

$game5 = new GameSet($game1->getWinner(), $game2->getWinner());
$game5->determineResult();

$game6 = new GameSet($game3->getWinner(), $game4->getWinner());
$game6->determineResult();

$game7 = new GameSet($game5->getWinner(), $game6->getWinner());
$game7->determineResult();

echo '---------------4 FINAL---------------' . "\n";
echo "{$game1->getAttacker()->getName()}({$game1->getAttackerPoints()}) VS {$game1->getDefender()->getName()}({$game1->getDefenderPoints()}) | Winner: {$game1->getWinner()->getName()}" . "\n";
echo "{$game2->getAttacker()->getName()}({$game2->getAttackerPoints()}) VS {$game2->getDefender()->getName()}({$game2->getDefenderPoints()}) | Winner: {$game2->getWinner()->getName()}" . "\n";
echo "{$game3->getAttacker()->getName()}({$game3->getAttackerPoints()}) VS {$game3->getDefender()->getName()}({$game3->getDefenderPoints()}) | Winner: {$game3->getWinner()->getName()}" . "\n";
echo "{$game4->getAttacker()->getName()}({$game4->getAttackerPoints()}) VS {$game4->getDefender()->getName()}({$game4->getDefenderPoints()}) | Winner: {$game4->getWinner()->getName()}" . "\n";
echo '--------------SEMI FINAL--------------' . "\n";
echo "{$game5->getAttacker()->getName()}({$game5->getAttackerPoints()}) VS {$game5->getDefender()->getName()}({$game5->getDefenderPoints()}) | Winner: {$game5->getWinner()->getName()}" . "\n";
echo "{$game6->getAttacker()->getName()}({$game6->getAttackerPoints()}) VS {$game6->getDefender()->getName()}({$game6->getDefenderPoints()}) | Winner: {$game6->getWinner()->getName()}" . "\n";
echo '----------------FINAL-----------------' . "\n";
echo "{$game7->getAttacker()->getName()}({$game7->getAttackerPoints()}) VS {$game7->getDefender()->getName()}({$game7->getDefenderPoints()}) | Winner: {$game7->getWinner()->getName()}" . "\n";

$game7 = new GameSet($game7->getWinner(), $game7->getWinner);
$game7->determineResult();
