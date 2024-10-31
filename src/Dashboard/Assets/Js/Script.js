jQuery(document).ready(function () {
  const iframe = document.getElementById("badges-iframe");
  const BADGES_GLOBAL = window.MYSMBWP_GLOBAL;

  const postmessagePayload = {
    currencyFormat: BADGES_GLOBAL.currencyFormat ?? "",
    url: BADGES_GLOBAL.restBase ?? "",
    tidioId: BADGES_GLOBAL.tidio || "",
    clientSite: BADGES_GLOBAL.clientSite || "",
    email: BADGES_GLOBAL.email || "",
    purchaseCodeLink: BADGES_GLOBAL.purchaseCodeLink || "",
    productName: BADGES_GLOBAL.productName || "",
    endpointVerification: BADGES_GLOBAL.endpointVerification || "",
    purchaseCode: BADGES_GLOBAL.purchaseCode || "",
    restBase: BADGES_GLOBAL.restBase ?? "",
    token: BADGES_GLOBAL.token ?? ""
  };

  function authen() {
    jQuery.ajax({
      data: {
        action: "mskmbwp_getCodeAuth", //Tên action, dữ liệu gởi lên cho server
      },
      method: "POST",
      url: ajaxurl,
      success: function (response) {
        iframe.addEventListener("load", function () {
          iframe.contentWindow.postMessage(
            {
              payload: {
                ...postmessagePayload,
                token: response.data.code,
              },
              type: "@InitializationPage/success",
            },
            "*"
          );
          iframe.classList.remove("hidden");
        });

        // check trường hợp login thành công
        if (iframe) {
          iframe.contentWindow.postMessage(
            {
              payload: { ...postmessagePayload, token: response.data.code },
              type: "@InitializationPage/success",
            },
            "*"
          );
        }
      },
      error: function (response) {
        iframe.addEventListener("load", function () {
          iframe.contentWindow.postMessage(
            {
              payload: {
                ...postmessagePayload,
                token: "",
              },
              type: "@InitializationPage/success",
            },
            "*"
          );
          iframe.classList.remove("hidden");
        });
      },
    });
  }

  authen();

  window.addEventListener(
    "message",
    (event) => {
      if (event.source !== iframe.contentWindow) {
        return;
      }
      const { type, payload } = event.data;
      console.log("type", type);

      if (type === "@InitializationPage/getTemplate") {
        iframe.contentWindow.postMessage(
          {
            payload: {
              template: "wordpress",
            },
            type: "@InitializationPage/sendTemplate",
          },
          "*"
        );
      }

      if (type === "@HasPassed") {
        if (payload.hasPassed === true) {
          authen();
        }
      }

      // manual products
      if (type === "@ProductPage/manualProductRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_getManualProducts",
            params: payload,
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  fullProducts: {
                    items: response.data.items,
                    hasNextPage: response.data.items.length > 0,
                    maxPages: response.data.maxPages,
                    currentPage: response.data.currentPage
                  }
                },
                type: "@ProductPage/manualProductSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      if (type === "@ProductPage/manualProductLoadMoreRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_loadMoreManualProducts",
            params: {
              paged: payload.paged + 1
            },
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  fullProducts: {
                    items: response.data.items,
                    hasNextPage: response.data.items.length > 0 ? true : false,
                    maxPages: response.data.maxPages,
                    currentPage: response.data.currentPage
                  }
                },
                type: "@ProductPage/manualProductLoadMoreSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      // full product
      if (type === "@ProductPage/fullProductRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_getFullProducts",
            params: payload,
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  fullProducts: {
                    items: response.data.items,
                    hasNextPage: response.data.items.length > 0 ? true : false,
                    maxPages: response.data.maxPages,
                    currentPage: response.data.currentPage
                  }
                },
                type: "@ProductPage/fullProductSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      if (type === "@ProductPage/fullProductLoadMoreRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_getFullProducts",
            params: {
              paged: payload.paged + 1
            },
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  fullProducts: {
                    items: response.data.items,
                    hasNextPage: response.data.items.length > 0 ? true : false,
                    maxPages: response.data.maxPages,
                    currentPage: response.data.currentPage
                  }
                },
                type: "@ProductPage/fullProductLoadMoreSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      // manual badges
      if (type === "@BadgesPage/getBadgesRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_getDefaultBadges",
            params: payload,
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  data: {
                    items: response.data.items,
                    maxPages: response.data.maxPage,
                    page: response.data.page
                  }
                },
                type: "@BadgesPage/getBadgesSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      if (type === "@BadgesPage/loadMoreBadgesRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_loadMoreDefaultBadges",
            params: {
              paged: payload.page + 1
            },
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  data: {
                    items: response.data.items,
                    maxPages: response.data.maxPage,
                    page: response.data.page
                  }
                },
                type: "@BadgesPage/loadMoreBadgesSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      if (type === "@CUDBadge/createBadgesRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_createManualBadges",
            params: {
              badgeUrl: payload.badge_id,
              config: payload.config,
              productIDs: payload.productIds,
              slugs: payload.slug
            },
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  data: response.data.items,
                  message: response.message
                },
                type: "@CUDBadge/createBadgesSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      if (type === "@CUDBadge/deleteBadgesRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_deleteManualBadges",
            params: {
              ids: payload.id
            },
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  id: response.data.id,
                  message: response.message
                },
                type: "@CUDBadge/deleteBadgesSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      if (type === "@CUDBadge/updateBadgesRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_updateManualBadges",
            params: {
              ...payload,
              badgeUrl: payload.badge_id || payload.badgeUrl,
              slugs: payload.slug,
              productIDs: payload.productIds,
              ids: payload.id
            },
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  data: response.data.items,
                  message: response.message
                },
                type: "@CUDBadge/updateBadgesSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      // automatics
      if (type === "@Automatic/getAutomaticBadgesRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_getAutomatics",
            params: payload,
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  data: response.data.items,
                },
                type: "@Automatic/getAutomaticBadgesSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      if (type === "@MYSHOPKIT_PRODUCT_BADGE_PURCHASE_CODE") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: BADGES_GLOBAL.purchaseCodeAction,
            params: payload,
          },
          success: function (response) {
          },
          error: function (jqXHR, error, errorThrown) {
            // alert(jqXHR.responseJSON.message);
          },
        });
      }

      if (type === "@Automatic/sortListPostType") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_sortAutomatics",
            params: { priority: payload.listPostType ?? [] },
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: response,
                type: "sortAutomatics/success",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      if (type === "@CUDAutomatic/createAutomaticRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_createBadgeAutomatic",
            params: {
              ...payload,
              badgeUrl: payload.baseUrl || payload.badgeUrl
            },
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  id: response.data.id,
                  description: response.data.description
                },
                type: "@CUDAutomatic/createAutomaticSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      if (type === "@CUDAutomatic/deleteAutomaticRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_deleteBadgeAutomatic",
            params: payload,
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  id: response.data.id,
                  message: response.message
                },
                type: "@CUDAutomatic/deleteAutomaticSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }

      if (type === "@CUDAutomatic/updateAutomaticRequest") {
        jQuery.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: "mskmbwp_updateBadgeAutomatic",
            params: {
              ...payload,
              badgeUrl: payload.baseUrl || payload.badgeUrl
            },
          },
          success: function (response) {
            iframe.contentWindow.postMessage(
              {
                payload: {
                  id: response.data.id,
                  description: response.data.description,
                  message: response.message
                },
                type: "@CUDAutomatic/updateAutomaticSuccess",
              },
              "*"
            );
          },
          error: function (jqXHR, error, errorThrown) {
            alert(jqXHR.responseJSON.message);
          },
        });
      }
    },
    false
  );

  jQuery("#btn-Revoke-Purchase-Code").click(function () {
    let status = confirm("Are you sure you want to revoke the Purchase Code?");
    if (status) {
      jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: {
          action: "wookit_revokePurchaseCode",
          purchaseCode: BADGES_GLOBAL.purchaseCode,
        },
        success: function (response) {
          location.reload();
        },
        error: function (jqXHR, error, errorThrown) {
          alert(jqXHR.responseJSON.message);
        },
      });
    }
  });
});
