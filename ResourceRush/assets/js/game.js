// Game state
const game = {
    resources: {
        wood: 0,
        stone: 0,
        food: 0,
        gold: 0
    },
    buildings: {
        woodcutter: 0,
        quarry: 0,
        farm: 0,
        market: 0
    },
    buildingCosts: {
        woodcutter: { wood: 10, stone: 5, food: 0, gold: 0 },
        quarry: { wood: 15, stone: 5, food: 0, gold: 0 },
        farm: { wood: 10, stone: 0, food: 0, gold: 0 },
        market: { wood: 25, stone: 15, food: 10, gold: 0 }
    },
    production: {
        woodcutter: { wood: 1 },
        quarry: { stone: 1 },
        farm: { food: 1 },
        market: { gold: 1 }
    },
    // Base gathering amounts
    gatherAmounts: {
        wood: 1,
        stone: 1,
        food: 1
    },
    // Game settings
    settings: {
        updateInterval: 1000 // Update production every second
    }
};

// DOM Elements
const elements = {
    resources: {
        wood: document.querySelector('#wood .resource-value'),
        stone: document.querySelector('#stone .resource-value'),
        food: document.querySelector('#food .resource-value'),
        gold: document.querySelector('#gold .resource-value')
    },
    buttons: {
        gatherWood: document.getElementById('gather-wood'),
        gatherStone: document.getElementById('gather-stone'),
        gatherFood: document.getElementById('gather-food'),
        build: document.querySelectorAll('.build-btn')
    },
    buildingCounts: {
        woodcutter: document.querySelector('#woodcutter .building-count'),
        quarry: document.querySelector('#quarry .building-count'),
        farm: document.querySelector('#farm .building-count'),
        market: document.querySelector('#market .building-count')
    }
};

// Initialize the game
function initGame() {
    updateUI();
    setupEventListeners();
    startProductionLoop();
}

// Update UI elements
function updateUI() {
    // Update resource displays
    for (const resource in game.resources) {
        if (elements.resources[resource]) {
            elements.resources[resource].textContent = Math.floor(game.resources[resource]);
        }
    }

    // Update building counts
    for (const building in game.buildings) {
        if (elements.buildingCounts[building]) {
            elements.buildingCounts[building].textContent = game.buildings[building];
        }
    }

    // Update build buttons (enable/disable based on resources)
    elements.buttons.build.forEach(button => {
        const buildingType = button.dataset.building;
        const costs = game.buildingCosts[buildingType];
        let canBuild = true;

        for (const resource in costs) {
            if (game.resources[resource] < costs[resource]) {
                canBuild = false;
                break;
            }
        }

        button.disabled = !canBuild;
    });
}

// Set up event listeners
function setupEventListeners() {
    // Resource gathering buttons
    elements.buttons.gatherWood.addEventListener('click', () => gatherResource('wood'));
    elements.buttons.gatherStone.addEventListener('click', () => gatherResource('stone'));
    elements.buttons.gatherFood.addEventListener('click', () => gatherResource('food'));

    // Building buttons
    elements.buttons.build.forEach(button => {
        button.addEventListener('click', () => {
            const buildingType = button.dataset.building;
            buildStructure(buildingType);
        });
    });
}

// Gather resources manually
function gatherResource(resourceType) {
    game.resources[resourceType] += game.gatherAmounts[resourceType];
    updateUI();
}

// Build a structure
function buildStructure(buildingType) {
    const costs = game.buildingCosts[buildingType];

    // Check if player has enough resources
    let canBuild = true;
    for (const resource in costs) {
        if (game.resources[resource] < costs[resource]) {
            canBuild = false;
            break;
        }
    }

    if (canBuild) {
        // Deduct resources
        for (const resource in costs) {
            game.resources[resource] -= costs[resource];
        }

        // Add building
        game.buildings[buildingType]++;

        // Update UI
        updateUI();
    }
}

// Production loop (runs every second)
function startProductionLoop() {
    setInterval(() => {
        // Calculate production from buildings
        for (const building in game.buildings) {
            const count = game.buildings[building];
            const production = game.production[building];

            for (const resource in production) {
                game.resources[resource] += production[resource] * count * (game.settings.updateInterval / 1000);
            }
        }

        updateUI();
    }, game.settings.updateInterval);
}

// Initialize the game when the page loads
document.addEventListener('DOMContentLoaded', initGame);