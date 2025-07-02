function ajax_post(form, callback) {
    const action = form.getAttribute('action');
    const method = form.getAttribute('method') || 'POST';
    const formData = new FormData(form);
    return fetch(action, {
        method: method,
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json(); // 解析 JSON
    })
    .then(data => {
        if (typeof callback === 'function') {
            callback(data); // 成功时调用 callback(data)
        }
        return data; // 仍然返回 data 以便链式调用
    })
    .catch(error => {
        console.error('Fetch error:', error);
        throw error; // 继续抛出错误，以便外部可以 .catch()
    });
}