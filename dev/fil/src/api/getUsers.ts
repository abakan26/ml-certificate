import {API_URL} from './constans';

export default async function getUsers(yes, no, date = new Date(), datePeriod = 'before', wpmLevel = 'start') {
    const body = new FormData();
    body.append('yes',  JSON.stringify(yes));
    body.append('no', JSON.stringify(no));
    if (date) {
        let month = date.getMonth() + 1
        let day = date.getDate();
        let dateF = date.getFullYear()
            + '-' + (month > 9 ? month.toString() : "0" + month)
            + '-' + (day > 9 ? day.toString() : "0" + day.toString());
        body.append('date', dateF);
        body.append('datePeriod', datePeriod);
        body.append('wpmLevel', wpmLevel);
    }
    const response = await fetch(API_URL + '?action=ml_get_filtered_users',
        {
            method: 'POST',
            body
        }
    );
    let data = await response.json();
    return data.users;
}
