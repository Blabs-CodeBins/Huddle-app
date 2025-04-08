<div class="panel panel-default">
    <div class="row tw-flex ">
        <div class="col-md-1 checkbox-sec">
            <input type="checkbox" data-category="On submission" data-todoid="${todo.id}" id="todotask${todo.id}" name="todotask[]" value="${todo.task_id}" class="todocheckbox ${hideTodocheckbox}">
            <span class="help-request ${hideHelprequest}"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
        </div>
        <div class="col-md-11 content-sec">
            <div class="row">
                <div class="task-name tw-pl-2 tw-pr-2">
                    <a href="<?php echo admin_url(); ?>/tasks/view/${todo.task_id}" onclick="init_task_modal(${todo.task_id});return false;">
                        <span class="inline-block full-width tw-truncate">#${todo.task_id} - ${todo.task_name}</span>
                    </a>
                </div>
                <div class="project-name tw-pl-2 tw-pr-2 tw-mb-2">
                    ${projectHtml}
                </div>
            </div>
            <div class="row">
                <div class="user-profile col-lg-2 col-sm-3 tw-pl-2 tw-pr-2 tw-mb-2">
                    <div class="tw-flex -tw-space-x-1">
                        <a href="<?php echo admin_url(); ?>profile/${todo.staff_id}">
                            ${todo.img_tag}
                        </a>
                        ${addeditmembers}
                    </div>
                </div>
                <div class="task-status col-lg-4 col-sm-9  tw-mt-2 !tw-p-0">
                    <span class="${color}" data-toggle="tooltip" title="" data-original-title="Status-${statusLabel}">
                        <i class="${icon}"></i> ${statusLabel}
                    </span>
                </div>
                <div class="task-date col-lg-5 col-sm-10 tw-text-sm  tw-mt-2 !tw-p-0">
                    <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Start Date">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        ${startDate}
                    </span>
                    &nbsp;
                    <span class="tw-text-neutral-500" data-toggle="tooltip" title="" data-original-title="Due Date">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        ${dueDate}
                    </span>
                </div>
                <div class="task-action col-lg-1 col-sm-2 tw-mt-2 !tw-p-0">
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle actionMenu" type="button" id="actionMenu${todo.task_id}" data-toggle="dropdown" aria-expanded="true" style="color:#3b82f6;border:none;background: none;padding: 0px 5px; font-size:16px;">
                            <!-- <i class="fa fa-edit"></i> -->
                            <i class="fa fa-cog"></i>
                            <!-- Action <span class="caret"></span> -->
                        </button>
                       ${actionDropdwn}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>