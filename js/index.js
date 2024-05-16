async function fetchUserSelect() {
    const spinner = document.getElementById('overlay');
    spinner.classList.remove('hide');
    const response = await fetch('src/index.php');

    if (response.ok) {
        const users = await response.json();
        const userSelect = document.getElementById('users');

        users.forEach(user => {
            userSelect.innerHTML += `<option value= ${user.id} >  ${user.name}  </option>`;
        });
    }
    spinner.classList.add('hide');
}

document.addEventListener("DOMContentLoaded", fetchUserSelect);

async function fetchBalance(event) {
    event.preventDefault();

    const spinner = document.getElementById('overlay');
    const report = document.getElementById('report');

    spinner.classList.remove('hide');

    const userId = document.getElementById('users').value;
    const response = await fetch('src/balance.php?userId=' + userId);

    if (response.ok) {
        const balance = await response.json();
        const resultTable = document.getElementById('result-tbody');

        report.classList.remove('hide');
        resultTable.innerHTML = '';

        balance.forEach(item => {
            for (const [date, amount] of Object.entries(item)) {
                resultTable.innerHTML += `<tr><td>${date}</td><td>${amount.toFixed(2)}</td></tr>`;
            }
        });
    }

    spinner.classList.add('hide');
}

const submitBtn = document.getElementById('submit');
submitBtn.addEventListener('click', fetchBalance);