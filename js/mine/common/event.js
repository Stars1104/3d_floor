
var initEvent = function () {
	var main = this;

	main.drawObj = null;
	main.obj3D = null;

	this.init = function (drawObj) {
		main.drawObj = drawObj;

		this.initEnv();
		this.eventLeft();
		this.initShowMore();
		this.sliderEvent();
		this.shapeEvent();
		this.menuEvent();
	};

	this.initEnv = function () {
		if (getURLParameter("mode") == "back") {
			var json = localStorage.getItem("current_env");
			var title = localStorage.getItem("current_ttl");

			if (json) {
				main.drawObj.jsonToCanvas(json);
				$("#font_protitle").html(title);
			}

			localStorage.removeItem("current_env");
			localStorage.removeItem("current_ttl");
		}
	}

	this.eventLeft = function () {
		var TimerT = 0;
		var TimerB = 0;

		$("#move_controlT").mouseover(function () {
			TimerT = setInterval(function () {
				var mTop = $("#left_area").css("margin-top").replace("px", "") * 1 + 10;
				$("#left_area").css("margin-top", mTop + "px");
				main.initShowMore();
			}, 50);
		});

		$("#move_controlB").mouseover(function () {
			TimerB = setInterval(function () {
				var mTop = $("#left_area").css("margin-top").replace("px", "") * 1 - 10;
				$("#left_area").css("margin-top", mTop + "px");
				main.initShowMore();
			}, 50);
		});

		$("#btn_add_obj").click(function () {
			$("#option_area").css({ "display": "block" });
			$("#display_area").css({ "display": "none" });

			$(".popup").css({ "display": "none" });
			$("#add_object").css({ "display": "block" });

			$("#overlay").css("display", "block");
			$("#over_overlay").fadeIn();
		});

		$("#move_controlT").mouseout(function () { clearInterval(TimerT); });
		$("#move_controlB").mouseout(function () { clearInterval(TimerB); });
	};

	this.shapeEvent = function () {
		$("#btn_shape").click(function () {
			$(this).next("dd").toggle();
		});
	}

	this.initShowMore = function () {
		var tHeight = $(window).height() - 40;
		var lHeight = $("#left_area").height();
		var mTop = $("#left_area").css("margin-top").replace("px", "") * 1;
		var tTop = $(window).height() - 29;

		if (lHeight + mTop > tHeight) {
			$("#move_controlB").css("display", "block");
		}
		else {
			$("#move_controlB").trigger("mouseout");
			$("#move_controlB").css("display", "none");
		}

		if (mTop < 0)
			$("#move_controlT").css("display", "block");
		else {
			$("#move_controlT").trigger("mouseout");
			$("#move_controlT").css("display", "none");
		}
	};

	this.sliderEvent = function () {
		$("#left_slider").click(function () {
			if ($("#left_area").css("left").replace("px", "") == 0)
				$("#left_area,#move_controlT,#move_controlB").animate({ left: -210 });
			else
				$("#left_area,#move_controlT,#move_controlB").animate({ left: 0 });
		});

		$("#right_slider").click(function () {
			if ($("#right_area").css("right").replace("px", "") == 0)
				$("#right_area").animate({ right: -200 });
			else
				$("#right_area").animate({ right: 0 });
		});
	}

	this.menuEvent = function () {
		$("#ctrl_area").find("li").click(function () {
			var index = $(this).index();

			switch (index) {
				case 0:

					break;
				case 1:

					break;
				case 2:
					$("#creat_project").children(".overlay_title").children("span").html("Create New Project");
					$(".popup").css({ "display": "none" });
					$("#creat_project").css({ "display": "block" });
					$("#overlay").css("display", "inline");
					$("#over_overlay").fadeIn();
					break;
				case 3:
					$(".popup").css({ "display": "none" });
					$("#open_project").css({ "display": "block" });
					$("#overlay").css("display", "inline");
					$("#over_overlay").fadeIn();

					$.ajax(
						{
							type: "POST",
							url: "ajax.php",
							data: ({ mode: 'get_projectlist' }),
							cache: false,
							success: function (result) {
								$("#project_list").html(result);

								var descr = $("#project_list").children(":selected").attr("descr");

								$("#view_pdescr").html(descr);
							}
						});
					$("#btn_addprj").data("canv_data", main.drawObj);
					break;
				case 4:
					$("#btn_addprj").data("canv_data", "");
					$("#creat_project").children(".overlay_title").children("span").html("Save As Project");
					$(".popup").css({ "display": "none" });
					$("#creat_project").css({ "display": "block" });
					$("#overlay").css("display", "inline");
					$("#over_overlay").fadeIn();
					break;
				case 6:
					main.drawObj.removeOBJ();
					break;
				case 7:
					main.drawObj.cloneOBJ();
					break;
				case 8:

					if ($("#font_protitle").html() == "" && confirm("Please Save Project Before 3D Mode!")) {
						return;
					}

					main.drawObj.canvas.deactivateAll().renderAll();

					var unit = main.drawObj.unit;
					var fWidth = $("#txt_fwidth").val() * unit;
					console.log(fWidth);
					var fHeight = $("#txt_fdepth").val() * unit;
					var rate = main.drawObj.prevScale;
					var objJSON = main.drawObj.canvasToJson();

					main.obj3D = new Obj3D();

					localStorage.setItem("current_ttl", $("#font_protitle").html());
					localStorage.setItem("current_env", objJSON);

					main.obj3D.init("area_3d", unit);
					main.obj3D.setFloor(fWidth, fHeight);
					main.obj3D.initLoad(JSON.parse(objJSON));

					break;
				case 9:
					main.drawObj.mGroup();
					break;
				case 10:
					main.drawObj.uGroup();
					break;
			}
		});

		$("#img_close3d").click(function () {
			window.location.href = "index.php?mode=back";
		});

		$("#export_3d").click(function () {
			window.open(main.obj3D.renderer.domElement.toDataURL());
		});

		$("#txt_number").keyup(function () {

		});
	}
}

function getURLParameter(name) {
	return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [, ""])[1].replace(/\+/g, '%20')) || null
}