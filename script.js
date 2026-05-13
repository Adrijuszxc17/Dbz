const STORAGE_KEY = "zFusionRegisterName";
const GENDER_KEY = "zFusionGender";
const TOTAL_POINTS = 100;

const registerNameInput = document.querySelector("#register-name");
const fighterNameInput = document.querySelector("#fighter-name");
const nameTargets = document.querySelectorAll("#fighter-name-display, #character-preview-name, #game-player-name, #game-chat-name");
const genderInputs = document.querySelectorAll("input[name='gender-preview']");
const gameSilhouettes = document.querySelectorAll(".game-silhouette");
const statInputs = Array.from(document.querySelectorAll(".stat-range"));
const remainingPoints = document.querySelector("#remaining-points");

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

if (fighterNameInput || nameTargets.length) {
  const savedName = localStorage.getItem(STORAGE_KEY);

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
