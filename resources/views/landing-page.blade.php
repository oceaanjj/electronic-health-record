<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Electronic Health Record – BSN</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
    @font-face {
    font-family: 'CreatoDisplay';
    src: url('{{ asset('font/creato_display/CreatoDisplay-Regular.otf') }}') format('opentype');
    font-weight: 400; font-style: normal;
    }
    @font-face {
    font-family: 'CreatoDisplay';
    src: url('{{ asset('font/creato_display/CreatoDisplay-Medium.otf') }}') format('opentype');
    font-weight: 500; font-style: normal;
    }
    @font-face {
    font-family: 'CreatoDisplay';
    src: url('{{ asset('font/creato_display/CreatoDisplay-Bold.otf') }}') format('opentype');
    font-weight: 700; font-style: normal;
    }
    @font-face {
    font-family: 'CreatoDisplay';
    src: url('{{ asset('font/creato_display/CreatoDisplay-ExtraBold.otf') }}') format('opentype');
    font-weight: 800; font-style: normal;
    }
    @font-face {
    font-family: 'Trajan-pro';
    src: url('{{ asset('font/trajan-pro/TrajanPro-Regular.ttf') }}') format('truetype');
    font-weight: 400; font-style: normal;
    }
    @font-face {
    font-family: 'Trajan-pro';
    src: url('{{ asset('font/trajan-pro/TrajanPro-Bold.otf') }}') format('opentype');
    font-weight: 700; font-style: normal;
    }
</style>
@vite('resources/css/landing-page.css')
</head>
<body>

    {{-- Dreamy gradient blobs --}}
<div class="blob blob--1"></div>
<div class="blob blob--2"></div>
<div class="blob blob--3"></div>

<nav>
  <div class="nav-brand">
    <div class="nav-logo">
      <img src="{{ asset('img/ehr-logo.png') }}" alt="Logo" style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
    </div>
    <div class="nav-title">
      <h1>Electronic Health Record</h1>
      <p>Bachelor of Science in Nursing</p>
    </div>
  </div>
  <div class="nav-actions">
    <button class="btn btn-outline">Login</button>
    <button class="btn btn-solid">Download App</button>
  </div>
</nav>

<section class="hero">
  <div class="hero-text reveal">
    <h2>
      Transform Patient Care with Smarter
      <span class="highlight">Documentation &amp;</span>
      <span class="highlight">Real-Time Alerts</span>
    </h2>
    <p>
      Our Electronic Health Record system empowers nurses and clinicians
      with rapid documentation, intelligent decision support, and automated
      alerts that help improve patient outcomes.
    </p>
  </div>

  <div class="hero-visual reveal">
    <div class="mockup-wrapper">
      <div class="pill-nav">
        <button class="pill-btn active" onclick="goSlide(0)"><span class="pdot"></span>Documentation</button>
        <button class="pill-btn" onclick="goSlide(1)"><span class="pdot"></span>CDSS</button>
        <button class="pill-btn" onclick="goSlide(2)"><span class="pdot"></span>Reports</button>
        <button class="pill-btn" onclick="goSlide(3)"><span class="pdot"></span>Charts</button>
      </div>
      <div class="mockup-frame">
        <div class="slides-track" id="slidesTrack">
          <div class="slide"><img src="{{ asset('img/documentation.png') }}" alt="Documentation"></div>
          <div class="slide"><img src="{{ asset('img/cdss.png') }}" alt="CDSS"></div>
          <div class="slide"><img src="{{ asset('img/reports.png') }}" alt="Reports"></div>
          <div class="slide"><img src="{{ asset('img/charts.png') }}" alt="Charts"></div>
        </div>
      </div>
      <div class="dots-row">
        <div class="dot-i active" onclick="goSlide(0)"></div>
        <div class="dot-i" onclick="goSlide(1)"></div>
        <div class="dot-i" onclick="goSlide(2)"></div>
        <div class="dot-i" onclick="goSlide(3)"></div>
      </div>
    </div>
  </div>
</section>

<section class="features">

  <div class="feat-cards">
  <div class="feat-card reveal">
    <div class="feat-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#c8a040" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
      </svg>
    </div>
    <div class="feat-card-text"><h4>Instant Alerts</h4><p>Real-time alerts for every input</p></div>
  </div>

  <div class="feat-card reveal">
    <div class="feat-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#c8a040" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
        <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
      </svg>
    </div>
    <div class="feat-card-text"><h4>Patient Records</h4><p>Access and manage patient</p></div>
  </div>

  <div class="feat-card reveal">
    <div class="feat-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#c8a040" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
      </svg>
    </div>
    <div class="feat-card-text"><h4>Clinical Insights</h4><p>Helping you to make decisions</p></div>
  </div>

  <div class="feat-card reveal">
    <div class="feat-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#c8a040" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/>
      </svg>
    </div>
    <div class="feat-card-text"><h4>PDF</h4><p>Easily generate pdf record</p></div>
  </div>
</div>

  <div class="feat-blocks">
    <div class="feat-block feat-block--green reveal">
      <div class="feat-block-text">
        <h3>Seamless<br>Integration</h3>
        <ul class="feat-list">
          <li>Real-time alerts for every patient update.</li>
          <li>No offline access, ensuring data integrity and accuracy at all times.</li>
          <li>Facilitating timely interventions and improving patient outcomes.</li>
        </ul>
      </div>
      <div class="feat-block-img">
        <img src="{{ asset('img/phone1.png') }}" alt="Seamless Integration">
      </div>
    </div>

    <div class="feat-block feat-block--gold reveal">
      <div class="feat-block-text">
        <h3>Paperless<br>Workflow</h3>
        <ul class="feat-list">
          <li>Automatic synchronization across web and app platforms.</li>
          <li>Paperless, efficient documentation for quicker, accurate data entry.</li>
          <li>Continuous access to patient records, ensuring seamless care delivery.</li>
        </ul>
      </div>
      <div class="feat-block-img">
        <img src="{{ asset('img/phone2.png') }}" alt="Paperless Workflow">
      </div>
    </div>
  </div>

</section>

<section class="team">
  <p class="team-eyebrow reveal">Built by</p>
  <h2 class="team-heading reveal">
    <span class="highlight highlight--pink">Computer Science</span> students,<br>
    for <span class="highlight highlight--gold">Nursing</span> students
  </h2>

  <div class="team-card reveal">
    <div class="team-members">
      <div class="team-member reveal">
        <div class="avatar-wrap" style="--avatar-bg:#E1FFE4; --avatar-border:#49D65B;">
          <img src="{{ asset('img/rain.png') }}" alt="Rain">
        </div>
        <h4>Rain</h4>
        <p>Developer</p>
      </div>
      <div class="team-member reveal">
        <div class="avatar-wrap" style="--avatar-bg:#CFECFF; --avatar-border:#0075C3;">
          <img src="{{ asset('img/rex.png') }}" alt="Rex">
        </div>
        <h4>Rex</h4>
        <p>Developer</p>
      </div>
      <div class="team-member reveal">
        <div class="avatar-wrap" style="--avatar-bg:#FFF0F1; --avatar-border:#FF098C;">
          <img src="{{ asset('img/jovilyn.png') }}" alt="Jovilyn">
        </div>
        <h4>Jovilyn</h4>
        <p>Developer</p>
      </div>
      <div class="team-member reveal">
        <div class="avatar-wrap" style="--avatar-bg:#FFEDEE; --avatar-border:#E81518;">
          <img src="{{ asset('img/keith.png') }}" alt="Keith">
        </div>
        <h4>Keith</h4>
        <p>Developer</p>
      </div>
      <div class="team-member reveal">
        <div class="avatar-wrap" style="--avatar-bg:#FFF6DF; --avatar-border:#EDB62C;">
          <img src="{{ asset('img/bryan.png') }}" alt="Bryan">
        </div>
        <h4>Bryan</h4>
        <p>Developer</p>
      </div>
    </div>
  </div>
</section>

<footer class="footer reveal">
  <div class="footer-brand">
    <div class="footer-logo">
      <img src="{{ asset('img/ehr-logo.png') }}" alt="EHR Logo">
    </div>
    <div class="footer-title">
      <h2>Electronic Health<br>Record</h2>
      <p>© 2026 Electronic Health Record</p>
    </div>
  </div>
</footer>

<button class="back-to-top" id="backToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
    fill="none" stroke="currentColor" stroke-width="2.5"
    stroke-linecap="round" stroke-linejoin="round">
    <polyline points="18 15 12 9 6 15"/>
  </svg>
</button>

<script>
  function goSlide(n){
    document.getElementById('slidesTrack').style.transform = `translateX(-${n*25}%)`;
    document.querySelectorAll('.pill-btn').forEach((b,i)=>b.classList.toggle('active',i===n));
    document.querySelectorAll('.dot-i').forEach((d,i)=>d.classList.toggle('active',i===n));
  }
  goSlide(0);

  // ── NAV GLASSMORPHISM ────────────────────────────────
    const nav = document.querySelector('nav');
    const toggleNav = () => nav.classList.toggle('scrolled', window.scrollY > 10);
    window.addEventListener('scroll', toggleNav);
    toggleNav(); // run on load in case page was refreshed mid-scroll

  // ── BACK TO TOP ──────────────────────────────────────
  const btn = document.getElementById('backToTop');
  const footer = document.querySelector('.footer');

  window.addEventListener('scroll', () => {
    btn.classList.toggle('visible', window.scrollY > 300);
    const footerTop = footer.getBoundingClientRect().top;
    btn.style.bottom = footerTop < window.innerHeight
      ? (window.innerHeight - footerTop + 16) + 'px'
      : '32px';
  });

  // ── SCROLL REVEAL ────────────────────────────────────
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('in-view');
        entry.target.classList.remove('out-view');
      } else {
        const rect = entry.target.getBoundingClientRect();
        if (rect.top >= 0) {
          // element is below viewport — scrolled back up, reset it
          entry.target.classList.remove('in-view');
          entry.target.classList.remove('out-view');
        } else {
          // element is above viewport — scrolled past going down
          entry.target.classList.add('out-view');
          entry.target.classList.remove('in-view');
        }
      }
    });
  }, { threshold: 0.12 });

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

  // ── HERO: already visible on load, trigger immediately ──
  setTimeout(() => {
    document.querySelectorAll('.reveal').forEach(el => {
      const rect = el.getBoundingClientRect();
      if (rect.top < window.innerHeight && rect.bottom > 0) {
        el.classList.add('in-view');
      }
    });
  }, 80);
</script>
</body>
</html>