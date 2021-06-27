{include common/header@ebcms/admin}
<div class="my-4 display-4">版本升级</div>
{if isset($version_now)}
{if isset($version_news) && $version_news}
<script>
    var EBCMS = {};
    $(function() {
        EBCMS.state = 0;
        EBCMS.vid = 0;
        EBCMS.stop = function(message) {
            EBCMS.console(message);
            EBCMS.console("<hr>");
            $("#progress_main .progress-bar").addClass('bg-danger').removeClass("progress-bar-animated").html(message);
            EBCMS.state = 0;
            $("#handler").removeClass('btn-warning').addClass('btn-primary').html('一键更新');
        };
        EBCMS.handler = function() {
            if (EBCMS.state) {
                EBCMS.state = 0;
            } else {
                if (confirm('更新前请做备份，确认立即更新吗？')) {
                    EBCMS.state = 1;
                    $("#progress_main").removeClass("d-none");
                    $("#handler").removeClass('btn-primary').addClass('btn-warning').html('一键停止');
                    $("#progress_main .progress-bar").removeClass('bg-danger').addClass("progress-bar-animated");
                    EBCMS.source();
                }
            }
        }
        EBCMS.source = function() {
            setTimeout(function() {
                EBCMS.process('10%', "版本检测...");
                $.ajax({
                    type: "GET",
                    url: "{$router->buildUrl('/ebcms/server/source')}",
                    dataType: "json",
                    success: function(response) {
                        if (response.code) {
                            if (!EBCMS.state) {
                                EBCMS.stop('已停止(检测完毕)');
                                return;
                            }
                            EBCMS.process('20%', "检测到新版本：" + response.data.version);
                            setTimeout(function() {
                                EBCMS.vid = response.data.id;
                                EBCMS.download();
                            }, 300);
                        } else {
                            EBCMS.stop("检测失败：" + response.message);
                        }
                    },
                    error: function(context) {
                        EBCMS.stop("检测失败：" + context.statusText);
                    }
                });
            }, 1000);
        };
        EBCMS.download = function() {
            EBCMS.process('30%', "开始下载~");
            setTimeout(function() {
                $.ajax({
                    type: "GET",
                    url: "{$router->buildUrl('/ebcms/server/download')}",
                    dataType: "json",
                    success: function(response) {
                        if (response.code) {
                            if (!EBCMS.state) {
                                EBCMS.stop('已停止(下载完毕)');
                                return;
                            }
                            EBCMS.process('40%', "下载完毕~");
                            setTimeout(function() {
                                EBCMS.backup();
                            }, 300);
                        } else {
                            EBCMS.stop("下载失败：" + response.message);
                        }
                    },
                    error: function(context) {
                        EBCMS.stop("下载失败：" + context.statusText);
                    }
                });
            }, 300);
        };
        EBCMS.backup = function() {
            EBCMS.process('50%', "备份中...");
            setTimeout(function() {
                $.ajax({
                    type: "GET",
                    url: "{$router->buildUrl('/ebcms/server/backup')}",
                    dataType: "json",
                    success: function(response) {
                        if (response.code) {
                            if (!EBCMS.state) {
                                EBCMS.stop('已停止(备份完成)');
                                return;
                            }
                            EBCMS.process('60%', "备份完毕~");
                            setTimeout(function() {
                                EBCMS.install();
                            }, 300);
                        } else {
                            EBCMS.stop("备份失败：" + response.message);
                        }
                    },
                    error: function(context) {
                        EBCMS.stop("备份失败：" + context.statusText);
                    }
                });
            }, 300);
        };
        EBCMS.install = function() {
            EBCMS.process('80%', "更新中...");
            setTimeout(function() {
                $.ajax({
                    type: "GET",
                    url: "{$router->buildUrl('/ebcms/server/install')}",
                    dataType: "json",
                    success: function(response) {
                        if (response.code) {
                            $("#v_" + EBCMS.vid).remove();
                            if (!EBCMS.state) {
                                EBCMS.stop('已停止(更新完成)');
                                return;
                            }
                            EBCMS.process('100%', "更新完毕，即将进行下一版本更新...");
                            setTimeout(function() {
                                EBCMS.process('0%', "<hr>");
                                EBCMS.source();
                            }, 1000);
                        } else {
                            EBCMS.stop("更新失败：" + response.message);
                        }
                    },
                    error: function(context) {
                        EBCMS.stop("更新失败：" + context.statusText);
                    }
                });
            }, 300);
        };
        EBCMS.console = function(message) {
            $(".console").append("<div>" + message + "</div>");
            $(".console").scrollTop(99999999);
        }
        EBCMS.process = function(width, tips) {
            EBCMS.console(tips);
            $("#progress_main .progress-bar").html(tips).width(width);
        }
    });
</script>
<div class="my-4">
    <button class="btn btn-primary" onclick="EBCMS.handler();" id="handler">一键更新</button>
</div>
<div id="progress_main" class="d-none my-4">
    <div class="version"></div>
    <div class="progress" style="height: 25px;">
        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
    </div>
    <style>
        .console {
            background-color: #000;
            height: 200px;
            width: 100%;
            overflow-y: auto;
        }
    </style>
    <div class="console mt-3 p-2 text-white">
    </div>
</div>
<dl>
    {foreach $version_news as $vo}
    <dd class="bg-light p-3 mt-3" id="v_{$vo.id}">
        <div class="h5">{$vo.version}</div>
        {:htmlspecialchars_decode($vo['content'])}
        <div><small class="text-muted">{$vo.update_time}</small></div>
    </dd>
    {/foreach}
</dl>
{else}
当前已经是最新版本
{/if}
{else}
<a href="http://www.ebcms.com" target="_blank">发生错误，去官方网站寻找帮助</a>
{/if}
{include common/footer@ebcms/admin}