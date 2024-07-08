var filename = "";

document.getElementById("formHospital").addEventListener("submit", function (event) {
    event.preventDefault();

    const year = document.querySelector('#formHospital select[name="h_year"]').value;
    const month = document.querySelector('#formHospital select[name="h_month"]').value;
    const productType = document.querySelector('#formHospital select[name="h_productType"]').value;

    // 获取表单中的提交按钮
    var submitBtn = document.querySelector('#formHospital button[type="submit"]');
    // 禁用按钮
    submitBtn.disabled = true;

    // layer显示一个加载动画
    var layerId = layer.load(1);
    // ajax请求
    fetch('index.php?action=hospital', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            year: year,
            month: month,
            productType: productType
        })
    }).then(response => {
        // return response.json()
        // 返回的是普通文本，所以不能用json()方法
        return response.text();
    }).then(data => {
        console.log(data);

        // 按行分割, 再按逗号分割
        const lines = data.split('\n')

        var tableString = "";
        var tableHeadString = "";
        var index = 0;

        for (const line of lines) {
            index++;
            const cells = line.split(',')
            // 如果是空行，跳过
            if (cells[0] == "") {
                continue;
            }

            if (index == 1) {
                tableHeadString = "<thead><tr>";
                for (const cell of cells) {
                    tableHeadString += `<th>${cell}</th>`;
                }

                tableHeadString += "</tr></thead>";
                tableString += tableHeadString;
            } else {
                tableString += "<tr>";
                for (const cell of cells) {
                    tableString += `<td title="${cell}">${cell}</td>`;
                }

                tableString += "</tr>";

            }
        }

        return tableString;
    }).then(tableString => {

        document.getElementById("list").innerHTML = tableString;
        filename = `${year}年${month}月-${productType}产品-医院数据.csv`;

        $.tablesort.defaults = {

            asc: 'sorted ascending',
            desc: 'sorted descending',

            compare: function (a, b) {

                // 如果a和b结尾是%，转换为数字比较
                if (a.endsWith('%') && b.endsWith('%')) {
                    a = parseFloat(a);
                    b = parseFloat(b);
                }

                // 如果是数字，转换为数字比较
                if (!isNaN(a) && !isNaN(b)) {
                    a = parseFloat(a);
                    b = parseFloat(b);
                }

                if (a > b) {
                    return 1;
                } else if (a < b) {
                    return -1;
                } else {
                    return 0;
                }
            }
        };

        $('#list').tablesort()
    }).catch(error => {
        console.error('Error:', error);
        layer.msg('分析失败，请稍后再试！')
    }).finally(() => {
        submitBtn.disabled = false;
        layer.close(layerId);
    });

    return false;
});

document.getElementById("fromPeople").addEventListener("submit", function (event) {
    event.preventDefault();

    const year = document.querySelector('#fromPeople select[name="p_year"]').value;
    const month = document.querySelector('#fromPeople select[name="p_month"]').value;
    const peopleType = document.querySelector('#fromPeople select[name="p_peopleType"]').value;
    const productType = document.querySelector('#fromPeople select[name="p_productType"]').value;

    // 获取表单中的提交按钮
    var submitBtn = document.querySelector('#formHospital button[type="submit"]');
    // 禁用按钮
    submitBtn.disabled = true;

    // layer显示一个加载动画
    var layerId = layer.load(1);

    // ajax请求
    fetch('index.php?action=people', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            year: year,
            month: month,
            peopleType: peopleType,
            productType: productType
        })
    }).then(response => {
        // return response.json()
        // 返回的是普通文本，所以不能用json()方法
        return response.text();
    }).then(data => {
        // console.log(data);

        // 按行分割, 再按逗号分割
        const lines = data.split('\n')

        var tableString = "";
        var tableHeadString = "";
        var index = 0;

        for (const line of lines) {
            index++;
            const cells = line.split(',')
            // 如果是空行，跳过
            if (cells[0] == "") {
                continue;
            }

            if (index == 1) {
                tableHeadString = "<thead><tr>";
                for (const cell of cells) {
                    tableHeadString += `<th>${cell}</th>`;
                }

                tableHeadString += "</tr></thead>";
                tableString += tableHeadString;
            } else {
                tableString += "<tr>";
                for (const cell of cells) {
                    tableString += `<td title="${cell}">${cell}</td>`;
                }

                tableString += "</tr>";

            }
        }

        return tableString;
    }).then(tableString => {

        document.getElementById("list").innerHTML = tableString;
        var peopleString = "";
        if (peopleType == "mgr") peopleString = "经理";
        else if (peopleType == "sale") peopleString = "销售";
        filename = `${year}年${month}月-${productType}产品-${peopleString}数据.csv`;

        $.tablesort.defaults = {

            asc: 'sorted ascending',
            desc: 'sorted descending',

            compare: function (a, b) {


                // 如果是数字，转换为数字比较
                if (!isNaN(a) && !isNaN(b)) {
                    a = parseFloat(a);
                    b = parseFloat(b);
                }


                if (a > b) {
                    return 1;
                } else if (a < b) {
                    return -1;
                } else {
                    return 0;
                }
            }
        };

        $('#list').tablesort()
    }).catch(error => {
        console.error('Error:', error);
        layer.msg('分析失败，请稍后再试！')
    }).finally(() => {
        submitBtn.disabled = false;
        layer.close(layerId);
    });

    return false;
});

document.getElementById("btnAnalysis").addEventListener("click", function (event) {

    event.preventDefault();

    // layer显示一个加载动画
    var layerId = layer.load(1);

    // ajax请求
    fetch('index.php?action=analysis', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({})
    }).then(response => {
        // return response.json()
        // 返回的是普通文本，所以不能用json()方法
        return response.text();
    }).then(data => {

        layer.open({
            title: '整合结果',
            content: data,
            shadeClose: true
        });

    }).finally(() => {
        layer.close(layerId);
    });

    return false;

});
document.getElementById("btnSpeed").addEventListener("click", function (event) {
    event.preventDefault();

    // layer显示一个加载动画
    var layerId = layer.load(1);

    // ajax请求
    fetch('index.php?action=speed', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({})
    }).then(response => {
        // return response.json()
        // 返回的是普通文本，所以不能用json()方法
        return response.text();
    }).then(data => {

        layer.open({
            title: '整合结果',
            content: data,
            shadeClose: true
        });

    }).finally(() => {
        layer.close(layerId);
    });

    return false;
});


document.getElementById("btnExport").addEventListener("click", function (event) {

    event.preventDefault();

    // 获取表格
    var table = document.querySelector("#list");
    var rows = table.querySelectorAll("tr");
    // 判断是否有数据
    if (rows.length == 0) {
        layer.msg('没有数据，无法导出！');
        return false;
    }

    // CSV内容
    var csv = [];

    // 遍历行
    rows.forEach(function (row) {
        var rowData = [];
        // 遍历列
        row.querySelectorAll("td, th").forEach(function (cell) {
            // 获取单元格文本，并处理可能存在的逗号
            var text = cell.innerText.replace(/,/g, '');
            rowData.push(text);
        });
        // 加入CSV数组
        csv.push(rowData.join(","));
    });

    // 将数组转换为CSV字符串，并处理换行
    var csvString = csv.join("\n");

    // 创建Blob对象
    var blob = new Blob(["\uFEFF" + csvString], {type: 'text/csv;charset=utf-8;'});

    // 创建下载链接
    var link = document.createElement("a");
    if (link.download !== undefined) { // feature detection
        // Browsers that support HTML5 download attribute
        var url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    // 弹一个成功的框, 带绿色图标 允许遮罩关闭
    layer.open({
        title: '导出成功',
        content: '文件已经成功导出, 请查看下载文件夹！',
        icon: 1,
        shadeClose: true
    });

    return false;

});

document.getElementById("btnExit").addEventListener("click", function (event) {

    event.preventDefault();

    // 访问一下 auth.php?action=logout
    fetch('auth.php?action=logout', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    }).then(response => {
        // return response.json()
        // 返回的是普通文本，所以不能用json()方法
        return response.text();
    }).then(data => {
        if (data === "ok") {
            window.location.href = "auth.php";
        }
    }).catch(error => {
        console.error('Error:', error);
        layer.msg('退出失败，请稍后再试！')
    });


    return false;

});