// script.js
document.addEventListener("DOMContentLoaded", function () {

  "use strict";

  /* =====================================
     القائمة الرئيسية للموبايل
  ===================================== */

  const menuToggle = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".main-nav");

  if (menuToggle && nav) {

    menuToggle.addEventListener("click", function () {

      const isOpen = nav.classList.toggle("open");

      menuToggle.classList.toggle("active", isOpen);

      menuToggle.setAttribute(
        "aria-expanded",
        String(isOpen)
      );

    });


    // إغلاق القائمة عند الضغط على أي رابط

    nav.querySelectorAll("a").forEach(function (link) {

      link.addEventListener("click", function () {

        nav.classList.remove("open");

        menuToggle.classList.remove("active");

        menuToggle.setAttribute(
          "aria-expanded",
          "false"
        );

      });

    });


    // إغلاق القائمة عند الضغط خارجها

    document.addEventListener("click", function (event) {

      if (
        !nav.contains(event.target) &&
        !menuToggle.contains(event.target)
      ) {

        nav.classList.remove("open");

        menuToggle.classList.remove("active");

        menuToggle.setAttribute(
          "aria-expanded",
          "false"
        );

      }

    });

  }


  /* =====================================
     ظهور العناصر عند التمرير
  ===================================== */

  const revealItems = document.querySelectorAll(".reveal");

  const reduceMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)"
  ).matches;


  if ("IntersectionObserver" in window && !reduceMotion) {

    const observer = new IntersectionObserver(

      function (entries, obs) {

        entries.forEach(function (entry) {

          if (entry.isIntersecting) {

            entry.target.classList.add("is-visible");

            obs.unobserve(entry.target);

          }

        });

      },

      {
        threshold: 0.12,
        rootMargin: "0px 0px -35px 0px"
      }

    );


    revealItems.forEach(function (item) {

      observer.observe(item);

    });

  } else {

    revealItems.forEach(function (item) {

      item.classList.add("is-visible");

    });

  }


  /* =====================================
     فلترة معرض الصور
  ===================================== */

  const filterButtons = document.querySelectorAll(".filter-btn");

  const galleryItems = document.querySelectorAll(".gallery-item");


  filterButtons.forEach(function (button) {

    button.addEventListener("click", function () {

      const filter = button.dataset.filter;


      // تغيير الزر النشط

      filterButtons.forEach(function (btn) {

        btn.classList.remove("active");

      });

      button.classList.add("active");


      // إظهار الصور المناسبة

      galleryItems.forEach(function (item) {

        const show =
          filter === "all" ||
          item.dataset.category === filter;

        item.hidden = !show;


        if (show) {

          item.classList.remove("is-visible");

          requestAnimationFrame(function () {

            item.classList.add("is-visible");

          });

        }

      });

    });

  });


  /* =====================================
     الأسئلة الشائعة
     فتح سؤال واحد فقط في كل مرة
  ===================================== */

  const faqDetails = document.querySelectorAll(
    ".faq-list details"
  );


  faqDetails.forEach(function (detail) {

    detail.addEventListener("toggle", function () {

      if (detail.open) {

        faqDetails.forEach(function (other) {

          if (other !== detail) {

            other.open = false;

          }

        });

      }

    });

  });


  /* =====================================
     السنة الحالية في الفوتر
  ===================================== */

  const year = document.getElementById("year");

  if (year) {

    year.textContent = new Date().getFullYear();

  }


  /* =====================================
     التعامل مع الصور غير الموجودة
  ===================================== */

  document.querySelectorAll(".gallery-item img").forEach(
    function (img) {

      img.addEventListener("error", function () {

        img.style.display = "none";

      });

    }
  );


  /* =====================================
     فحص الصور الرئيسية
  ===================================== */

  document.querySelectorAll(
    ".hero-photo img, .about-image img"
  ).forEach(function (img) {

    img.addEventListener("error", function () {

      console.warn(
        "تعذر تحميل الصورة:",
        img.getAttribute("src")
      );

    });

  });

});
