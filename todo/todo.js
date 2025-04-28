$(document).ready(function()
{
    $("#task-list-container").hide();
    $("#title-completed").hide();
    $("#completed-task-container").hide();

    var taskId = 0; // task count

    function createTaskCard(taskId)
    {
        // divcheck
        // divinnerdescription
        let checkbox = $('<input>', {
            class: 'check',
            id: 'check' + taskId,
            type: 'checkbox'
        }).on('change', function () {
            const taskCard = $(this).closest('.task-card');
            if ($(this).is(':checked')) {
                $('#completed-task-container').append(taskCard);
                $("#title-completed").show();
                $("#completed-task-container").show();
            } else {
                $('#task-list-container').append(taskCard);
            }
        });

        let divCheck = $('<div>', {
            class: 'div-check'
        });

        divCheck.append(checkbox);

        let labelTaskId = $('<label>', {
            class: 'label-task-id',
            id: 'label-task-id' + taskId,
            text: taskId.toString()
        });

        let label = $('<label>', {
            class: 'task-label',
            id: 'task-label' + taskId,
            text: $("#task-input").val()
        });// Task Description

        let inputEdit = $('<input>', {
            class: 'input-edit',
            id: 'input-edit' + taskId,
            value: label.text()
        });
        inputEdit.hide();

        let confirmEditBtn = $('<button>', {
            class: 'confirm-edit',
            id: 'confirm-edit' + taskId,
            text: 'Confirm'
        }).on('click', function(){
            inputEdit.hide();
            $(this).hide();
            label.text(inputEdit.val());
            label.show();
        });
        confirmEditBtn.hide();

        let innerDivDescription = $('<div>', {
            class: 'inner-div-description'
        });

        let divDescription = $('<div>', {
            class: 'div-description'
        });

        innerDivDescription.append(labelTaskId, label, inputEdit, confirmEditBtn);
        divDescription.append(divCheck, innerDivDescription);

        let editBtn = $('<button>', {
            class: 'btn-edit',
            id: 'btn-edit' + taskId,
            title: 'Edit'
        }).append($('<img>', {
            src: 'pencil.png'
        })
        ).on('click', function(){
            inputEdit.show();
            confirmEditBtn.show();
            label.hide();
        });

        let delBtn = $('<button>', {
            class: 'btn-del',
            id: 'btn-del' + taskId,
            title: 'Delete'
        }).append($('<img>',{
            src: 'bin.png'
        })).on('click', function(){
            $(this).closest(".task-card").remove();
        });

        let divCommand = $('<div>', {
            class: 'div-command'
        });

        divCommand.append(editBtn, delBtn);

        let taskCard = $('<div>', {
            class: 'task-card',
            id: 'task-card' + taskId
        });

        $("#task-list-container").append(taskCard);

        taskCard.append(divDescription, divCommand);

        if (taskId==1)$("#task-list-container").show();
    }

    $("#btn-add-task").on('click', function()
    {
        taskId++;

        createTaskCard(taskId);

        $("#task-input").val("");

        if (taskId>0)$("#no-task-label-container").hide();

    });
});