import {API_URL} from './constans';

export default async function getFile($userIds: Array<string>) {
    const body = new FormData();
    body.append('userIds', $userIds.join());
    const response = await fetch(API_URL + "?action=ml_get_file", {
        method: 'POST',
        body
    })
    const link = await response.text();
   window.open(link);
}
