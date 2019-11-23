let $doctor, $date, $specialty, $hours;
let iRadio;

const noHoursAlert = `
<div class="alert alert-danger" role="alert">
    <strong>Lo sentimos!</strong> No se encontraron horas disponible para el médico en el día seleccionado
</div>
`

$(function (){
    $specialty = $('#specialty');
    $doctor = $('#doctor');
    $date = $('#scheduled_date');
    $hours = $('#hours');

    $specialty.change(()=> {
        const specialtyId = $specialty.val();
        const url = `/api/specialties/${specialtyId}/doctors`;
        $.getJSON(url, onDoctorsLoaded);
    });

    $doctor.change(loadHours);
    $date.change(loadHours);
});

function onDoctorsLoaded(doctors){
    let htmlOptions = '';
    doctors.forEach(function (doctor){
        htmlOptions += `<option value="${doctor.id}">${doctor.name}</option>`;
    });
    $doctor.html(htmlOptions);
    loadHours(); //side-effect
}

function loadHours() {
    const selectedDate = $date.val();
    const doctorId = $doctor.val();
    const url = `/api/schedule/hours?date=${selectedDate}&doctor_id=${doctorId}`;
    $.getJSON(url, displayHours);
}

function displayHours(data) {

    if(!data.morning && !data.afternoon){
       $hours.html(noHoursAlert);
        return;
    }

    let htmlHours = '';
    iRadio = 0;

    if(data.morning) {
        const morning_intervals = data.morning;
        morning_intervals.forEach(interval => {
            htmlHours += getRadioHtml(interval);
        });
    }

    if(data.afternoon) {
        const afternoon_intervals = data.afternoon;
        afternoon_intervals.forEach(interval => {
            htmlHours += getRadioHtml(interval);
        });
    }
    $hours.html(htmlHours);
}

function getRadioHtml(interval){
    const text = `${interval.start} - ${interval.end}`;
    return `<div class="custom-control custom-radio mb-3">
              <input name="scheduled_time" class="custom-control-input" id="scheduled_time${iRadio}" type="radio" value="${interval.start}" required="">
              <label class="custom-control-label" for="scheduled_time${iRadio++}">${text}</label>
            </div>`;
}
