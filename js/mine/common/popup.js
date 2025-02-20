var popupObj = function () {
	var main = this;

	main.drawObj = null;

	this.init = function (drawObj) {
		this.initEvt();
		this.drawObj = drawObj;
	}

	this.initEvt = function () {
		$(".popup").draggable();
		$(".overlay_close").click(function () {
			$("#overlay").fadeOut();
			$("#over_overlay").css("display", "none");
		});

		$("#btn_select_mode").click(function () {
			var mode = $("#option_area").find(":checked").parents("li").index();

			$("#option_area").css({ "display": "none" });
			$("#display_area").css({ "display": "block" });

			if (mode == 0) {
				$("#append_new").css({ "display": "" });
				$("#create_new").css({ "display": "none" });
			}
			else {
				$("#append_new").css({ "display": "none" });
				$("#create_new").css({ "display": "" });
			}
		});

		$("#txt_fbgimage").click(function () {
			$(".popup").css("display", "none");
			$("#overlay_image").css({ "display": "" });
			$("#overlay").css("display", "inline");
			$("#over_overlay").fadeIn();
		});

		$("#btn_useimage").click(function () {
			$(".preview_area").html("<img src='" + $("#img_url").val() + "'>");
		});

		$("#project_list").on("change", function () {
			var descr = $(this).children(":selected").attr("descr");
			var update_time = $(this).children(":selected").attr("update");

			update_time = "(Last Updated : " + update_time + ")";

			$("#view_pdescr").html(descr);
			$("#last_updated").html(update_time);
		});

		$("#upload_btn").click(function () {
			$("#loading").ajaxStart(function () { $(this).show(); });
			$("#loading").ajaxComplete(function () { $(this).hide(); });

			$.ajaxFileUpload(
				{
					url: 'php/doajaxfileupload.php',
					secureuri: false,
					fileElementId: 'fileToUpload',
					dataType: 'json',
					data: { name: 'logan', id: 'id' },
					success: function (data, status) {
						if (typeof (data.error) != 'undefined') {
							if (data.error != '') {
								alert(data.error);
							} else {
								$(".preview_area").html(data.msg);
							}
						}
					},
					error: function (data, status, e) {
						alert(e);
					}
				});

			return false;
		});

		$("#btn_addimg").click(function () {
			var url = $(".preview_area").children("img").attr("src");

			if ($(".preview_area").html() == "") {
				alert("Please select one file");
				return;
			}

			$("#txt_fbgimage").val(url);
			$("#overlay").fadeOut();
			$("#over_overlay").css("display", "none");

			$("#canvas").css({ "background-image": "url(" + url + ")" });
		});

		$("#btn_addprj").click(function () {
			var title = $("#project_title").val();
			var descr = $("#project_descr").val();
			var data = $(this).data("canv_data").canvasToJson();

			if (title == "") {
				alert("Please input title");
				return;
			}

			if ($("#creat_project").children(".overlay_title").children("span").html() == "Create New Project")
				main.drawObj.canvas.clear().renderAll();

			$.ajax(
				{
					type: "POST",
					url: "ajax.php",
					data: ({ mode: 'create_project', title: title, descr: descr, data: data }),
					cache: false,
					success: function (result) {
						if (result == -1) {
							alert("The project name you inputed already Exist!");
							return;
						}

						projectID = result;

						$("#overlay").fadeOut();
						$("#over_overlay").css("display", "none");
						$("#font_protitle").html(title);

						alert("Successfullyl Saved!");
					}
				});
		});

		$("#btn_selprj").click(function () {
			var data = $("#project_list").children(":selected").attr("data");
			var obj = [];
			var title = $("#project_list").children(":selected").html();

			if (data) obj = JSON.parse(data);

			$("#font_protitle").html(title);

			projectID = $("#project_list").children(":selected").val();

			if (obj.length == 0) {
				$("#overlay").fadeOut();
				$("#over_overlay").css("display", "none");

				main.drawObj.canvas.clear().renderAll();

				return;
			}

			main.drawObj.jsonToCanvas(data);

			$("#overlay").fadeOut();
			$("#over_overlay").css("display", "none");
		});

		$("#btn_addnew").click(function () {
			if ($("#append_new").css("display") != "none") {
				var append_id = "";

				$("#object_list").find(":checked").each(function () {
					append_id += $(this).attr("info") + ",";
				});


				$.ajax(
					{
						type: "POST",
						url: "ajax.php",
						data: ({ mode: 'append_object', data: append_id }),
						cache: false,
						success: function (result) {
							window.location.href = "index.php";
						}
					});
			}
			else {
				var mtl = "";
				let filePath = "";


				let fileInput = document.getElementById('Thum_upload_file').files[0].name;
				let fileInput1 = document.getElementById('2d_upload_file').files[0].name;
				let fileInput2 = document.getElementById('3d_upload_file').files[0].name;
				let fileInput3 = document.getElementById('3d_mat_upload_file').files[0].name;
				// let fileInput4 = document.getElementById('3d_fix_upload_file').files[0].name;

				if (fileInput2 == 'Chir_whiteMap.obj') filePath = 'white_simple_chair';
				if (fileInput2 == 'rounded table with white map cover 2M with 11 chairs.obj') filePath = 'mass_round_table';
				if (fileInput2 == 'squire table with White map cover 2M with 12 chairs.obj') filePath = 'mass_square_table';
				if (fileInput2 == 'TableSq_2_4m_black_noMap.obj') filePath = 'black_square_table';
				if (fileInput2 == 'Table_01.obj') filePath = 'white_round_table';
				if (fileInput2 == '7m_WASHINGTONIA PALM_tree.obj') filePath = 'Washington_tree';

				var html = "";
				html += fileInput + ',' + fileInput1 + ',' + fileInput2 + ',' + fileInput3 + ',' + filePath + "," + $("#obj_width").val() + "," + $("#obj_height").val();

				// 	html 	+= $("#add_object").find(".content_part:nth-child(2)").find("p").children("span").html() + ",";
				// 	html 	+= $("#add_object").find(".content_part:nth-child(3)").find("p").children("span").html() + ",";
				// 	html 	+= $("#add_object").find(".content_part:nth-child(4)").find("p").children("span").html() + ",";
				// 	html 	+= $("#add_object").find(".content_part:nth-child(5)").find("p").children("span").html() + ",";
				// 	html 	+= $("#obj_name").val() + ",";
				// 	html 	+= $("#obj_width").val() + ",";
				// 	html 	+= $("#obj_height").val() + ",";

				$("#add_object").find(".content_part:nth-child(6)").find("p").children("span").each(function () {
					mtl += $(this).html() + ";";
				});

				if ($("#obj_name").html == "") {
					alert("Please type in OBJ Name!");
					return;
				}

				if (!fileInput) {
					alert("Please select Thumbnail!");
					return;
				}

				if (!fileInput1) {
					alert("Please select 2D Image!");
					return;
				}

				if (!fileInput2) {
					alert("Please select 3D Object!");
					return;
				}

				if (!fileInput3) {
					alert("Please select 3D Material!");
					return;
				}



				html += mtl
				$.ajax(
					{
						type: "POST",
						url: "ajax.php",
						data: ({ mode: 'create_object', data: html }),
						cache: false,
						success: function (result) {
							console.log(`😂😂😂${JSON.stringify(result)}`)
							var ret = JSON.parse(result);

							if (ret.mode == "error") {
								alert(ret.msg);
							}
							else {
								window.location.href = "index.php";
							}
						}
					});
			}
		});
	}
}