document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector("[data-sidebar]");
  const toggle = document.querySelector("[data-sidebar-toggle]");

  if (toggle && sidebar) {
    toggle.addEventListener("click", () => sidebar.classList.toggle("open"));

    document.addEventListener("click", (event) => {
      if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
        sidebar.classList.remove("open");
      }
    });
  }

  document.querySelectorAll('input[type="number"]').forEach((input) => {
    input.addEventListener("wheel", (event) => event.preventDefault(), {
      passive: false,
    });
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const customSelects = document.querySelectorAll("[data-custom-select]");

  const closeSelect = (select) => {
    const button = select.querySelector("[data-custom-select-button]");
    const menu = select.querySelector("[data-custom-select-menu]");

    select.classList.remove("is-open");

    if (button) {
      button.setAttribute("aria-expanded", "false");
    }

    if (menu) {
      menu.hidden = true;
    }
  };

  const closeAllSelects = (except = null) => {
    customSelects.forEach((select) => {
      if (select !== except) {
        closeSelect(select);
      }
    });
  };

  customSelects.forEach((select) => {
    const input = select.querySelector("[data-custom-select-input]");
    const button = select.querySelector("[data-custom-select-button]");
    const label = select.querySelector("[data-custom-select-label]");
    const menu = select.querySelector("[data-custom-select-menu]");
    const search = select.querySelector("[data-custom-select-search]");
    const options = Array.from(
      select.querySelectorAll("[data-custom-select-option]"),
    );

    if (!input || !button || !label || !menu) {
      return;
    }

    const resetSearch = () => {
      if (!search) {
        return;
      }

      search.value = "";

      options.forEach((option) => {
        option.hidden = false;
      });
    };

    const openSelect = () => {
      closeAllSelects(select);

      select.classList.add("is-open");
      button.setAttribute("aria-expanded", "true");
      menu.hidden = false;

      resetSearch();

      if (search) {
        setTimeout(() => search.focus(), 30);
      }
    };

    const toggleSelect = () => {
      if (select.classList.contains("is-open")) {
        closeSelect(select);
      } else {
        openSelect();
      }
    };

    const selectOption = (option) => {
      input.value = option.dataset.value ?? "";
      label.textContent = option.dataset.label ?? "";

      options.forEach((item) => item.classList.remove("is-selected"));
      option.classList.add("is-selected");

      input.dispatchEvent(new Event("change", { bubbles: true }));
      closeSelect(select);
      button.focus();
    };

    button.addEventListener("click", (event) => {
      event.preventDefault();
      toggleSelect();
    });

    button.addEventListener("keydown", (event) => {
      if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        toggleSelect();
      }

      if (event.key === "Escape") {
        closeSelect(select);
      }

      if (event.key === "ArrowDown") {
        event.preventDefault();
        openSelect();

        const firstVisibleOption = options.find((option) => !option.hidden);
        firstVisibleOption?.focus();
      }
    });

    if (search) {
      search.addEventListener("click", (event) => {
        event.stopPropagation();
      });

      search.addEventListener("input", () => {
        const query = search.value.trim().toLowerCase();

        options.forEach((option) => {
          const labelText = (option.dataset.label ?? "").toLowerCase();
          const valueText = (option.dataset.value ?? "").toLowerCase();

          option.hidden =
            !labelText.includes(query) && !valueText.includes(query);
        });
      });

      search.addEventListener("keydown", (event) => {
        if (event.key === "ArrowDown") {
          event.preventDefault();

          const firstVisibleOption = options.find((option) => !option.hidden);
          firstVisibleOption?.focus();
        }

        if (event.key === "Escape") {
          closeSelect(select);
          button.focus();
        }
      });
    }

    options.forEach((option) => {
      option.addEventListener("click", () => {
        selectOption(option);
      });

      option.addEventListener("keydown", (event) => {
        if (event.key === "Enter" || event.key === " ") {
          event.preventDefault();
          selectOption(option);
        }

        if (event.key === "Escape") {
          closeSelect(select);
          button.focus();
        }
      });
    });
  });

  document.addEventListener("click", (event) => {
    customSelects.forEach((select) => {
      if (!select.contains(event.target)) {
        closeSelect(select);
      }
    });
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const datePickers = document.querySelectorAll("[data-date-picker]");

  const monthNames = [
    "يناير",
    "فبراير",
    "مارس",
    "أبريل",
    "مايو",
    "يونيو",
    "يوليو",
    "أغسطس",
    "سبتمبر",
    "أكتوبر",
    "نوفمبر",
    "ديسمبر",
  ];

  const pad = (number) => String(number).padStart(2, "0");

  const formatDate = (date) => {
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(
      date.getDate(),
    )}`;
  };

  const parseDate = (value) => {
    if (!value) {
      return null;
    }

    const parts = value.split("-").map(Number);

    if (parts.length !== 3 || parts.some(Number.isNaN)) {
      return null;
    }

    return new Date(parts[0], parts[1] - 1, parts[2]);
  };

  const isSameDay = (first, second) => {
    return (
      first &&
      second &&
      first.getFullYear() === second.getFullYear() &&
      first.getMonth() === second.getMonth() &&
      first.getDate() === second.getDate()
    );
  };

  const closePicker = (picker) => {
    const button = picker.querySelector("[data-date-picker-button]");
    const menu = picker.querySelector("[data-date-picker-menu]");

    picker.classList.remove("is-open");

    if (button) {
      button.setAttribute("aria-expanded", "false");
    }

    if (menu) {
      menu.hidden = true;
    }
  };

  const closeAllPickers = (except = null) => {
    datePickers.forEach((picker) => {
      if (picker !== except) {
        closePicker(picker);
      }
    });
  };

  datePickers.forEach((picker) => {
    const input = picker.querySelector("[data-date-picker-input]");
    const button = picker.querySelector("[data-date-picker-button]");
    const label = picker.querySelector("[data-date-picker-label]");
    const menu = picker.querySelector("[data-date-picker-menu]");
    const title = picker.querySelector("[data-date-picker-title]");
    const daysContainer = picker.querySelector("[data-date-picker-days]");
    const prevButton = picker.querySelector("[data-date-picker-prev]");
    const nextButton = picker.querySelector("[data-date-picker-next]");
    const todayButton = picker.querySelector("[data-date-picker-today]");
    const clearButton = picker.querySelector("[data-date-picker-clear]");

    if (!input || !button || !label || !menu || !title || !daysContainer) {
      return;
    }

    let selectedDate = parseDate(input.value);
    let viewDate = selectedDate ? new Date(selectedDate) : new Date();

    const setDate = (date) => {
      selectedDate = date;
      input.value = formatDate(date);
      label.textContent = input.value;
      input.dispatchEvent(new Event("change", { bubbles: true }));
      render();
      closePicker(picker);
      button.focus();
    };

    const render = () => {
      const year = viewDate.getFullYear();
      const month = viewDate.getMonth();

      title.textContent = `${monthNames[month]} ${year}`;
      daysContainer.innerHTML = "";

      const firstDay = new Date(year, month, 1);
      const startDay = firstDay.getDay();
      const startDate = new Date(year, month, 1 - startDay);
      const today = new Date();

      for (let index = 0; index < 42; index++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + index);

        const dayButton = document.createElement("button");
        dayButton.type = "button";
        dayButton.className = "date-picker__day";
        dayButton.textContent = date.getDate();

        if (date.getMonth() !== month) {
          dayButton.classList.add("is-muted");
        }

        if (isSameDay(date, today)) {
          dayButton.classList.add("is-today");
        }

        if (isSameDay(date, selectedDate)) {
          dayButton.classList.add("is-selected");
        }

        dayButton.addEventListener("click", () => {
          setDate(date);
        });

        daysContainer.appendChild(dayButton);
      }
    };

    const openPicker = () => {
      closeAllPickers(picker);
      picker.classList.add("is-open");
      button.setAttribute("aria-expanded", "true");
      menu.hidden = false;
      render();
    };

    button.addEventListener("click", (event) => {
      event.preventDefault();

      if (picker.classList.contains("is-open")) {
        closePicker(picker);
      } else {
        openPicker();
      }
    });

    prevButton?.addEventListener("click", () => {
      viewDate.setMonth(viewDate.getMonth() - 1);
      render();
    });

    nextButton?.addEventListener("click", () => {
      viewDate.setMonth(viewDate.getMonth() + 1);
      render();
    });

    todayButton?.addEventListener("click", () => {
      const today = new Date();
      viewDate = new Date(today);
      setDate(today);
    });

    clearButton?.addEventListener("click", () => {
      selectedDate = null;
      input.value = "";
      label.textContent = "اختر التاريخ";
      input.dispatchEvent(new Event("change", { bubbles: true }));
      render();
      closePicker(picker);
    });
  });

  document.addEventListener("click", (event) => {
    datePickers.forEach((picker) => {
      if (!picker.contains(event.target)) {
        closePicker(picker);
      }
    });
  });
});
