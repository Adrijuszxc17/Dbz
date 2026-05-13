const STORAGE_KEY = "zFusionRegisterName";
const TOTAL_POINTS = 100;

const registerNameInput = document.querySelector("#register-name");
const fighterNameInput = document.querySelector("#fighter-name");
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

if (fighterNameInput) {
  const savedName = localStorage.getItem(STORAGE_KEY);

  if (savedName) {
    fighterNameInput.value = savedName;
  }
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
