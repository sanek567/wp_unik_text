"use strict";

jQuery(document).ready(function () {
  jQuery("#wpUnikTextSynonimizeEditor").click(function () {
    let editorContent = "";
    let editorTitle = "";
    let tinyName = "";
    if (
      typeof wp != "undefined" &&
      wp.hasOwnProperty("data") &&
      wp.data.hasOwnProperty("select") &&
      wp.data.select("core/editor") != undefined
    ) {
      if (
        wp.data.select("core/editor").getBlockCount() == 1 &&
        wp.data.select("core/editor").getBlocks()[0].name == "core/freeform"
      ) {
        tinyName =
          "editor-" + wp.data.select("core/editor").getBlocks()[0].clientId;

        if (tinyMCE.get(tinyName) != null) {
          editorContent = tinyMCE.get(tinyName).getContent();
        } else {
          editorContent = wp.data
            .select("core/editor")
            .getEditedPostAttribute("content");
        }
      } else {
        editorContent = wp.data
          .select("core/editor")
          .getEditedPostAttribute("content");
      }

      //gutenberg

      editorTitle = wp.data
        .select("core/editor")
        .getEditedPostAttribute("title");
    } else {
      //oldMCE
      editorContent = tinyMCE.get("content").getContent();
      editorTitle = jQuery("#title").val();
    }

    const contentObject = {
      content: editorContent,
      title: editorTitle,
    };
    const response = document.querySelector('#response');
    let xhrContent = new XMLHttpRequest();
    xhrContent.open(
      "POST",
      "../wp-content/plugins/wp_unik_text/wpUnikText_hand.php"
    );

    xhrContent.setRequestHeader("Content-Type", "application/json");
    const contentJson = JSON.stringify(contentObject);

    xhrContent.send(contentJson);

    xhrContent.addEventListener("load", () => {
      if (xhrContent.status === 200) {
        let contentOut = xhrContent.response;
        contentOut = JSON.parse(contentOut);
        jQuery("#fieldWpUnikTextRewrittenTitle").val(contentOut.title);
        response.textContent = ArrIn.Changed + " " + contentOut.prozent + " " + "%"+ " " + ArrIn.text;
        tinymce
          .get("wpuniktextEditor")
          .setContent(
            tinymce.get("wpuniktextEditor").getContent() + contentOut.content
          );
      }
    });
  });

  jQuery("#wpUnikTextSynonimizeEditorSend").click(function () {
    if (
      typeof wp != "undefined" &&
      wp.hasOwnProperty("data") &&
      wp.data.hasOwnProperty("select") &&
      typeof wp.data.select("core/editor") != "undefined" &&
      jQuery(".editor-post-title__input").length != 0
    ) {
      //title set
        wp.data
          .dispatch("core/editor")
          .editPost({
            title: jQuery("#fieldWpUnikTextRewrittenTitle").val(),
          });

      //content
      let getContent = tinyMCE.get("wpuniktextEditor").getContent();
      getContent = getContent.replace(
        /<span class="synonym.*?>(.*?)<\/span>/g,
        "$1"
      );
      wp.data.dispatch("core/editor").editPost({ content: getContent });

      let parsedBlocks = wp.blocks.parse(getContent);
      wp.data.dispatch("core/editor").resetBlocks([]);
      wp.data.dispatch("core/editor").insertBlocks(parsedBlocks);
    } else {
      // TinyMCE

      //title
        jQuery("#title").val(
          jQuery("#fieldWpUnikTextRewrittenTitle").val()
        );

      //content
      jQuery("#content-tmce").trigger("click");
      tinyMCE
        .get("content")
        .setContent(tinyMCE.get("wpuniktextEditor").getContent());
      tinymce.triggerSave();
    }

    jQuery("#content_ifr")
      .contents()
      .find(".synonym")
      .each(function () {
        jQuery(this).replaceWith(jQuery(this).html());
      });
    jQuery("html, body,.interface-interface-skeleton__content").animate({
      scrollTop: 0,
    });
    return false;
  });
});
