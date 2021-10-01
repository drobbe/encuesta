const url = "http://localhost/index.php/";
let unlockResult = false;
let blockALl = false;

function validaterFirstLevel() {
  if (document.getElementById("name").value !== "") return true;
  return false;
}
function validateThirdLevel() {
  if (document.querySelector('input[name="hobby"]:checked').value === "Ninguno") return false;
  return true;
}

function back() {
  unlockResult = false;
  blockALl = false;
  fullpage_api.moveTo(1, 1);
}

let myFullpage = new fullpage("#fullpage", {
  onLeave: function (origin, destination, direction) {
    if (blockALl === true) return false;

    if (destination.index === 1 && validaterFirstLevel() !== true) {
      return false;
    }

    if (destination.index === 3 && validateThirdLevel() !== true) {
      console.log(direction);
      if (direction === "down") fullpage_api.moveTo(5, 0);
      if (direction === "up") fullpage_api.moveTo(3, 0);

      return false;
    }

    console.log(unlockResult);
    if (destination.index === 5 && unlockResult === false) {
      return false;
    }
  },
  lockAnchors: false,
  sectionsColor: ["#1bbc9b", "#00c192", "#00a37b", "#008665", "#007558", "#006e52"],
  lazyLoad: true,
});

function generateResult() {
  let direccion = `${url}/encuesta`;
  const hobby = document.querySelector('input[name="hobby"]:checked').value;
  const payload = {
    nombre: document.getElementById("name").value,
    genero: document.querySelector('input[name="genero"]:checked').value,
    hobby: hobby,
    tiempo: hobby === "Ninguno" ? null : document.querySelector('input[name="tiempo"]:checked').value,
  };

  fetch(direccion, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
  })
    .then((response) => response.json())
    .then((data) => {
      setTimeout(() => {
        blockALl = true;
      }, 500);

      let xValuesGender = ["Mujeres", "Hombres"];
      let yValuesGender = [0, 0];
      let barColorsGender = ["#C5003E", "#78AA00"];
      let xValues = [];
      let yValues = [];

      let xValuesTime = [];
      let yValuesTime = [];

      data.forEach((d) => {
        d.genero === "mujer" ? yValuesGender[0]++ : yValuesGender[1]++;
        let index = xValues.findIndex((x) => x === d.hobby);
        if (index === -1) {
          xValues.push(d.hobby);
          yValues.push(1);
        } else {
          yValues[index] = yValues[index] + 1;
        }

        index = xValuesTime.findIndex((x) => x === d.tiempo);
        if (d.tiempo !== null) {
          if (index === -1) {
            xValuesTime.push(d.tiempo);
            yValuesTime.push(1);
          } else {
            yValuesTime[index] = yValuesTime[index] + 1;
          }
        }
      });

      unlockResult = true;
      fullpage_api.moveTo(6, 0);

      let barColors = ["#4F1025", "#C5003E", "#D9FF5B", "#78AA00", "#1BBC9B"];

      console.log(xValues, yValues);

      new Chart("genero", {
        type: "pie",
        data: {
          labels: xValuesGender,
          datasets: [
            {
              backgroundColor: barColorsGender,
              data: yValuesGender,
            },
          ],
        },
        options: {
          plugins: {
            legend: {
              labels: {
                filter: (e) => false,
              },
              title: {
                font: {
                  size: "20rem",
                },
                display: true,
                text: "Genero",
                color: "white",
              },
            },
          },
        },
      });

      new Chart("hobbys", {
        type: "pie",
        data: {
          labels: xValues,
          datasets: [
            {
              backgroundColor: barColors,
              data: yValues,
            },
          ],
        },
        options: {
          plugins: {
            legend: {
              labels: {
                filter: (e) => false,
              },
              title: {
                font: {
                  size: "20rem",
                },
                display: true,
                text: "Hobbys",
                color: "white",
              },
            },
          },
        },
      });

      new Chart("horas", {
        type: "pie",
        data: {
          labels: xValuesTime,
          datasets: [
            {
              backgroundColor: barColors,
              data: yValuesTime,
            },
          ],
        },
        options: {
          plugins: {
            legend: {
              labels: {
                filter: (e) => false,
              },
              title: {
                font: {
                  size: "20rem",
                },
                display: true,
                text: "Horas de Hobby",
                color: "white",
              },
            },
          },
        },
      });

      let tabledata = data;
      let table = new Tabulator("#table", {
        height: 205, // set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
        data: tabledata, //assign data to table
        layout: "fitColumns",
        columns: [
          //Define Table Columns
          { title: "Nombre", field: "nombre" },
          { title: "Genero", field: "genero" },
          { title: "Hobby", field: "hobby" },
          { title: "tiempor Hobby", field: "tiempo" },
        ],
        rowClick: function (e, row) {
          //trigger an alert message when the row is clicked
          //alert("Row " + row.getData().id + " Clicked!!!!");
        },
      });
    })
    .catch((error) => {
      console.error(error);
      alert("Couldn");
    });
}

function excel() {
  let direccion = `${url}/excel`;
  window.open(direccion, "_blank").focus();
}
