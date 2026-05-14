const STORAGE_KEY = "zFusionRegisterName";
const GENDER_KEY = "zFusionGender";
const TOTAL_POINTS = 100;

const serverName = document.body.dataset.userName;
const serverGender = document.body.dataset.userGender;
const registerNameInput = document.querySelector("#register-name");
const fighterNameInput = document.querySelector("#fighter-name");
const nameTargets = document.querySelectorAll("#fighter-name-display, #character-preview-name, #game-player-name, #game-chat-name, #inventory-player-name, #fight-player-name");
const genderInputs = document.querySelectorAll("input[name='gender']");
const gameSilhouettes = document.querySelectorAll(".game-silhouette");
const bagItems = document.querySelectorAll(".bag-item");
const dropSlots = document.querySelectorAll(".drop-slot");
const inventoryHint = document.querySelector("#inventory-hint");
const statInputs = Array.from(document.querySelectorAll(".stat-range"));
const remainingPoints = document.querySelector("#remaining-points");
const fightActions = document.querySelectorAll("[data-fight-action]");
const resetFightButton = document.querySelector("#reset-fight");

if (registerNameInput) {
  const saveRegisterName = () => {
    const value = registerNameInput.value.trim();

    if (value) {
      localStorage.setItem(STORAGE_KEY, value);
    }
  };

  registerNameInput.addEventListener("input", saveRegisterName);
  registerNameInput.form?.addEventListener("submit", saveRegisterName);
}

if (serverName && serverName !== "Vardas bus įkeltas") {
  localStorage.setItem(STORAGE_KEY, serverName);
}

if (serverGender) {
  localStorage.setItem(GENDER_KEY, serverGender);
}

if (fighterNameInput || nameTargets.length) {
  const savedName = localStorage.getItem(STORAGE_KEY) || serverName;

  if (savedName) {
    if (fighterNameInput) {
      fighterNameInput.value = savedName;
    }

    nameTargets.forEach((target) => {
      target.textContent = savedName;
    });
  } else {
    nameTargets.forEach((target) => {
      target.textContent = "Pirma užsiregistruok pagrindiniame puslapyje";
    });
  }
}

if (genderInputs.length) {
  const savedGender = localStorage.getItem(GENDER_KEY);

  genderInputs.forEach((input) => {
    if (input.value === savedGender) {
      input.checked = true;
    }

    input.addEventListener("change", () => {
      if (input.checked) {
        localStorage.setItem(GENDER_KEY, input.value);
      }
    });
  });
}

if (gameSilhouettes.length) {
  const savedGender = localStorage.getItem(GENDER_KEY) || "male";

  gameSilhouettes.forEach((image) => {
    const shouldShow =
      (savedGender === "male" && image.classList.contains("game-silhouette-male")) ||
      (savedGender === "female" && image.classList.contains("game-silhouette-female"));

    image.hidden = !shouldShow;
  });
}

if (bagItems.length && dropSlots.length) {
  let draggedItem = null;

  const setInventoryHint = (message) => {
    if (inventoryHint) {
      inventoryHint.textContent = message;
    }
  };

  bagItems.forEach((item) => {
    item.addEventListener("dragstart", (event) => {
      draggedItem = item;
      item.classList.add("is-dragging");
      event.dataTransfer.setData("text/plain", item.dataset.slot);
      event.dataTransfer.effectAllowed = "copy";
      setInventoryHint(`Tempiamas: ${item.dataset.item}`);
    });

    item.addEventListener("dragend", () => {
      item.classList.remove("is-dragging");
      draggedItem = null;
    });
  });

  dropSlots.forEach((slot) => {
    slot.addEventListener("dragover", (event) => {
      event.preventDefault();
      slot.classList.add("is-over");
    });

    slot.addEventListener("dragleave", () => {
      slot.classList.remove("is-over");
    });

    slot.addEventListener("drop", (event) => {
      event.preventDefault();
      slot.classList.remove("is-over");

      if (!draggedItem || draggedItem.dataset.slot !== slot.dataset.accept) {
        setInventoryHint("Šitas daiktas netinka šiam slotui.");
        return;
      }

      const equippedItem = draggedItem.cloneNode(true);
      equippedItem.removeAttribute("draggable");
      equippedItem.classList.remove("is-dragging");
      equippedItem.classList.add("is-equipped");
      slot.innerHTML = "";
      slot.append(equippedItem);
      setInventoryHint(`${draggedItem.dataset.item} įdėta į slotą.`);
    });
  });
}

if (fightActions.length) {
  const maxStats = {
    hp: 100,
    ki: 100,
    stamina: 100,
  };

  const player = {
    hp: 100,
    ki: 40,
    stamina: 80,
    defending: false,
    healUsed: false,
  };

  const npc = {
    hp: 100,
    ki: 30,
    stamina: 70,
    defending: false,
  };

  const fightLog = document.querySelector("#fight-log");
  const fightStateLabel = document.querySelector("#fight-state-label");
  const fightTurnLabel = document.querySelector("#fight-turn-label");

  const clamp = (value, max) => Math.max(0, Math.min(value, max));

  const updateBar = (id, value, max) => {
    const bar = document.getElementById(`${id}-bar`);
    const text = document.getElementById(`${id}-text`);

    if (bar) {
      bar.style.setProperty("--value", `${(value / max) * 100}%`);
    }

    if (text) {
      text.textContent = `${value}/${max}`;
    }
  };

  const addFightLog = (message) => {
    if (!fightLog) {
      return;
    }

    const entry = document.createElement("li");
    entry.textContent = message;
    fightLog.prepend(entry);
  };

  const updateFightUi = () => {
    updateBar("player-hp", player.hp, maxStats.hp);
    updateBar("player-ki", player.ki, maxStats.ki);
    updateBar("player-stamina", player.stamina, maxStats.stamina);
    updateBar("npc-hp", npc.hp, maxStats.hp);
    updateBar("npc-ki", npc.ki, maxStats.ki);
    updateBar("npc-stamina", npc.stamina, maxStats.stamina);
  };

  const setFightOver = (message) => {
    addFightLog(message);
    fightActions.forEach((button) => {
      button.disabled = true;
    });

    if (fightStateLabel) {
      fightStateLabel.textContent = "Finished";
    }

    if (fightTurnLabel) {
      fightTurnLabel.textContent = "Kova baigta";
    }
  };

  const npcTurn = () => {
    if (npc.hp <= 0 || player.hp <= 0) {
      return;
    }

    let damage = 8 + Math.floor(Math.random() * 9);

    if (npc.ki >= 25 && Math.random() > 0.55) {
      npc.ki = clamp(npc.ki - 25, maxStats.ki);
      damage += 8;
      addFightLog("NPC paleido Ki smūgį.");
    } else if (npc.stamina < 18) {
      npc.stamina = clamp(npc.stamina + 18, maxStats.stamina);
      npc.ki = clamp(npc.ki + 10, maxStats.ki);
      addFightLog("NPC atsitraukė ir atgavo resursus.");
      updateFightUi();
      return;
    } else {
      npc.stamina = clamp(npc.stamina - 10, maxStats.stamina);
      addFightLog("NPC smogė baziniu smūgiu.");
    }

    if (player.defending) {
      damage = Math.ceil(damage * 0.45);
      player.defending = false;
      addFightLog("Tavo gynyba sumažino žalą.");
    }

    player.hp = clamp(player.hp - damage, maxStats.hp);
    addFightLog(`Tu gavai ${damage} žalos.`);
    updateFightUi();

    if (player.hp <= 0) {
      setFightOver("Pralaimėjai kovą. Reikės daugiau treniruotis.");
    }
  };

  const playerAction = (action) => {
    if (player.hp <= 0 || npc.hp <= 0) {
      return;
    }

    if (fightTurnLabel) {
      fightTurnLabel.textContent = "NPC atsakas";
    }

    if (action === "attack") {
      if (player.stamina < 12) {
        addFightLog("Trūksta stamina smūgiui.");
        return;
      }

      player.stamina = clamp(player.stamina - 12, maxStats.stamina);
      const damage = 11 + Math.floor(Math.random() * 8);
      npc.hp = clamp(npc.hp - damage, maxStats.hp);
      addFightLog(`Smūgis pataikė. NPC gavo ${damage} žalos.`);
    }

    if (action === "ki") {
      if (player.ki < 25) {
        addFightLog("Trūksta Ki energijos.");
        return;
      }

      player.ki = clamp(player.ki - 25, maxStats.ki);
      const damage = 22 + Math.floor(Math.random() * 10);
      npc.hp = clamp(npc.hp - damage, maxStats.hp);
      addFightLog(`Ki banga pataikė. NPC gavo ${damage} žalos.`);
    }

    if (action === "defend") {
      player.defending = true;
      player.stamina = clamp(player.stamina + 6, maxStats.stamina);
      addFightLog("Tu pasiruošei gintis.");
    }

    if (action === "charge") {
      player.ki = clamp(player.ki + 22, maxStats.ki);
      player.stamina = clamp(player.stamina + 8, maxStats.stamina);
      addFightLog("Tu sukaupei Ki energiją.");
    }

    if (action === "heal") {
      if (player.healUsed) {
        addFightLog("Gyvybės kapsulė jau panaudota.");
        return;
      }

      player.healUsed = true;
      player.hp = clamp(player.hp + 24, maxStats.hp);
      addFightLog("Panaudojai gyvybės kapsulę ir atstatei HP.");
    }

    player.ki = clamp(player.ki + 4, maxStats.ki);
    updateFightUi();

    if (npc.hp <= 0) {
      setFightOver("Pergalė! NPC nugalėtas.");
      return;
    }

    window.setTimeout(() => {
      npcTurn();

      if (fightTurnLabel && player.hp > 0 && npc.hp > 0) {
        fightTurnLabel.textContent = "Žaidėjo ėjimas";
      }
    }, 450);
  };

  fightActions.forEach((button) => {
    button.addEventListener("click", () => playerAction(button.dataset.fightAction));
  });

  resetFightButton?.addEventListener("click", () => {
    window.location.reload();
  });

  updateFightUi();
}

if (statInputs.length && remainingPoints) {
  const getUsedPoints = () => statInputs.reduce((total, input) => total + Number(input.value), 0);

  const updatePointUi = () => {
    const usedPoints = getUsedPoints();
    remainingPoints.textContent = Math.max(TOTAL_POINTS - usedPoints, 0);

    statInputs.forEach((input) => {
      const output = document.getElementById(input.dataset.output);

      if (output) {
        output.textContent = input.value;
      }
    });
  };

  statInputs.forEach((input) => {
    input.dataset.previousValue = input.value;

    input.addEventListener("input", () => {
      const usedPoints = getUsedPoints();

      if (usedPoints > TOTAL_POINTS) {
        input.value = input.dataset.previousValue;
      } else {
        input.dataset.previousValue = input.value;
      }

      updatePointUi();
    });
  });

  updatePointUi();
}
