<?php

namespace Acquia\LightningExtension\Context;

use Behat\Behat\Context\Context;

/**
 * A context to automatically undo changes made during a scenario.
 */
class UndoContext implements Context {

  /**
   * Whether the stack is locked (i.e., operations cannot be pushed onto it).
   *
   * @var bool
   */
  protected $locked = FALSE;

  /**
   * The stack of operations to execute.
   *
   * Each operation is an array consisting of a callable and arguments to pass
   * to it, none of which can be passed by reference.
   *
   * @var array
   */
  protected $stack = [];

  /**
   * Executes all operations in the stack.
   *
   * @AfterScenario
   */
  public function runAll() {
    // Locking the stack prevents an infinite loop if any operations push to it.
    $this->locked = TRUE;

    while ($this->stack) {
      $operation = array_pop($this->stack);
      call_user_func_array($operation[0], $operation[1]);
    }

    $this->locked = FALSE;
  }

  /**
   * Pushes an operation onto the stack.
   *
   * If the stack is locked, this will have no effect.
   *
   * @param callable $callback
   *   The function to call.
   * @param mixed ...
   *   (optional) Arguments to pass to the function. None of the arguments can
   *   be passed by reference.
   */
  public function push(callable $callback, ...$arguments) {
    if ($this->locked == FALSE) {
      $this->stack[] = [$callback, $arguments];
    }
  }

}
