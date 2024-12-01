<?php
$count = $wire->getState('count') ?? 0;
?>

<div class="counter">
    <h2>Counter: <?php echo $count; ?></h2>

    <button
        wire:click="decrement"
        <?php echo $count === 0 ? 'disabled' : ''; ?>
    >
        -
    </button>

    <input
        type="number"
        wire:model="count"
        value="<?php echo $count; ?>"
        min="0"
    >

    <button wire:click="increment">
        +
    </button>
</div>