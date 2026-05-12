<style>
/* ── SHARED NAVBAR STYLES ── */
.navbar {
    padding: 14px 28px;
    background: #fff !important;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    position: sticky; top: 0; z-index: 100;
}
.navbar-brand {
    font-family: 'Fredoka One', cursive;
    font-size: 2rem; color: #0d5407 !important; letter-spacing: 1px;
}
.nav-link {
    font-weight: 700; font-size: 0.95rem;
    color: #999 !important; margin-right: 4px; transition: color 0.2s;
}
.navbar-nav .nav-item .nav-link.active {
    color: #0d5407 !important; font-weight: 900 !important; position: relative;
}
.navbar-nav .nav-item .nav-link.active::after {
    content: ''; position: absolute; bottom: -6px; left: 50%;
    transform: translateX(-50%); width: 24px; height: 3px;
    background: #0d5407; border-radius: 50px; display: block;
}
.nav-link:hover { color: #941717 !important; }

/* User icon + name */
.user-dropdown-toggle {
    display: flex; flex-direction: column; align-items: center;
    gap: 2px; text-decoration: none; cursor: pointer;
    background: none; border: none; padding: 0;
}
.user-dropdown-toggle .user-icon {
    font-size: 26px; color: #0d5407; line-height: 1;
}
.user-dropdown-toggle .user-name {
    font-family: 'Fredoka One', cursive;
    font-size: 0.85rem; color: #0d5407;
    letter-spacing: 0.3px; line-height: 1; font-weight: 900;
    max-width: 100px; overflow: hidden;
    text-overflow: ellipsis; white-space: nowrap;
}
</style>