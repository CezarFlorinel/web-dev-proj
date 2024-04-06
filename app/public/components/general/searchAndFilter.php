<?php
use App\Models\Enumerations\TypeOfGuns;

$typesOfGuns = TypeOfGuns::cases();
?>

<!-- Filter Section -->
<div class="filter-section mb-4">
    <div class="dropdown mb-2">
        <!-- Note the 'data-bs-toggle' instead of 'data-toggle' -->
        <button class="btn btn-secondary dropdown-toggle" type="button" id="gunTypeDropdown" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            Filter by Type
        </button>
        <ul class="dropdown-menu">
            <!-- Show All option -->
            <li><a class="dropdown-item" href="#" onclick="filterByType(''); return false;">Show All</a></li>
            <?php foreach ($typesOfGuns as $typeOfGun): ?>
                <li><a class="dropdown-item" href="#"
                        onclick="filterByType('<?php echo htmlspecialchars($typeOfGun->value, ENT_QUOTES, 'UTF-8'); ?>'); return false;">
                        <?php echo htmlspecialchars($typeOfGun->value, ENT_QUOTES, 'UTF-8'); ?>
                    </a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Search Bar -->
<div class="search-bar">
    <input type="text" class="form-control" id="gunSearchInput" placeholder="Search for guns by name...">
</div>