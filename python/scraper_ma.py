"""
========================================================================================
Scraper Direktori Putusan Mahkamah Agung RI (Tahun 2026 / Fleksibel)
Menggunakan curl_cffi untuk bypass proteksi Cloudflare (TLS Fingerprint Impersonation).
Cocok untuk integrasi Controller Laravel & pipeline RAG AI.
========================================================================================
"""

import sys
import json
import os
import re
import time
import argparse
from typing import List, Dict, Optional
from curl_cffi import requests
from bs4 import BeautifulSoup


class MahkamahAgungScraper:
    BASE_URL = "https://putusan3.mahkamahagung.go.id"

    def __init__(self, impersonate: str = "chrome", timeout: int = 30, delay: float = 1.5):
        """
        :param impersonate: Browser profile untuk bypass Cloudflare ("chrome", "safari", dll)
        :param timeout: Durasi timeout HTTP request dalam detik
        :param delay: Jeda waktu (detik) antar request halaman agar aman dari rate limiting
        """
        self.impersonate = impersonate
        self.timeout = timeout
        self.delay = delay
        self.headers = {
            "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
            "Accept-Language": "id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7",
            "DNT": "1",
            "Upgrade-Insecure-Requests": "1",
        }

    def fetch_page(self, url: str, max_retries: int = 3) -> Optional[str]:
        """Mengambil teks HTML dengan TLS impersonation dan penanganan retry saat rate limit."""
        impersonate_profiles = ["chrome", "safari15_5", "chrome"]
        for attempt in range(max_retries):
            try:
                prof = impersonate_profiles[attempt % len(impersonate_profiles)]
                response = requests.get(
                    url,
                    headers=self.headers,
                    impersonate=prof,
                    timeout=self.timeout
                )
                if response.status_code == 200 and len(response.text) > 1000:
                    return response.text
                elif response.status_code in (429, 403, 503):
                    # Rate limit dari Cloudflare MA, tunggu sejenak lalu coba kembali
                    time.sleep(1.0 * (attempt + 1))
                else:
                    return None
            except Exception:
                time.sleep(1.0 * (attempt + 1))

        return None

    def parse_list_page(self, html_content: str) -> List[Dict]:
        """
        Mengekstrak daftar putusan dari tab utama aktif (mengabaikan widget sidebar, popular post, footer, dll).
        """
        soup = BeautifulSoup(html_content, "html.parser")

        # Prioritas 1: Ambil kontainer tab putusan utama (#tabs-1 atau .col_two_third)
        container = soup.find("div", id="tabs-1")
        if not container:
            container = soup.find("div", class_="col_two_third")
        if not container:
            pagination = soup.find(class_="pagination")
            if pagination:
                container = pagination.find_parent("div", class_="tab-content") or pagination.find_parent("div", class_="postcontent") or soup
            else:
                container = soup.find("div", class_="tab-content") or soup

        entries = container.find_all("div", class_="entry-c")
        results = []
        seen_urls = set()

        for entry in entries:
            try:
                # 1. Judul & Link Detail Putusan
                link_elem = entry.find("a", href=lambda h: h and "/direktori/putusan/" in h)
                if not link_elem:
                    continue

                url_putusan = link_elem["href"]
                if not url_putusan.startswith("http"):
                    url_putusan = self.BASE_URL + url_putusan

                # Pastikan entry bukan bagian dari footer atau sidebar
                if any(p.name == "footer" or "col_one_third" in (p.get("class") or []) for p in entry.parents):
                    continue

                # Mencegah duplikasi data putusan
                if url_putusan in seen_urls:
                    continue
                seen_urls.add(url_putusan)

                judul = link_elem.get_text(strip=True)

                # 2. Hierarki Pengadilan & Klasifikasi Perkara (Breadcrumbs)
                pengadilan = ""
                klasifikasi = ""
                breadcrumb_div = entry.find("div", class_="small")
                if breadcrumb_div:
                    crumbs = [a.get_text(strip=True) for a in breadcrumb_div.find_all("a")]
                    if len(crumbs) > 1:
                        pengadilan = crumbs[1]
                    if len(crumbs) > 2:
                        klasifikasi = " > ".join(crumbs[2:])

                # 3. Tanggal Register, Putus, Upload
                tgl_register, tgl_putus, tgl_upload = "", "", ""
                meta_divs = entry.find_all("div", class_="small")
                for mdiv in meta_divs:
                    raw_text = mdiv.get_text(separator=" ", strip=True)
                    if "Register" in raw_text or "Putus" in raw_text or "Upload" in raw_text:
                        reg_m = re.search(r"Register\s*:\s*([\d-]+)", raw_text)
                        put_m = re.search(r"Putus\s*:\s*([\d-]+)", raw_text)
                        upl_m = re.search(r"Upload\s*:\s*([\d-]+)", raw_text)
                        if reg_m: tgl_register = reg_m.group(1)
                        if put_m: tgl_putus = put_m.group(1)
                        if upl_m: tgl_upload = upl_m.group(1)

                # 4. Ringkasan Para Pihak & Amar
                ringkasan = ""
                detail_div = entry.find("div", class_=False)
                if detail_div:
                    raw_detail = detail_div.get_text(separator=" ", strip=True)
                    # Bersihkan karakter separator non-standar dari direktori MA
                    cleaned = re.sub(r'[\x00-\x1f\x7f-\x9f\ufffd]', ' ', raw_detail)
                    cleaned = re.sub(r'\s*[-—–]\s*', ' — ', cleaned)
                    # Hapus penanda view & download counter di ujung string
                    cleaned = re.sub(r'\d+\s*—\s*\d+\s*$', '', cleaned)
                    ringkasan = re.sub(r'\s+', ' ', cleaned).strip()

                results.append({
                    "judul": judul.encode("utf-8", "ignore").decode("utf-8"),
                    "url": url_putusan,
                    "pengadilan": pengadilan.encode("utf-8", "ignore").decode("utf-8"),
                    "klasifikasi": klasifikasi.encode("utf-8", "ignore").decode("utf-8"),
                    "tgl_register": tgl_register,
                    "tgl_putus": tgl_putus,
                    "tgl_upload": tgl_upload,
                    "ringkasan": ringkasan,
                })
            except Exception:
                continue

        return results

    def fetch_putusan_detail(self, url_putusan: str) -> Dict:
        """Mengambil detail lengkap satu putusan dan URL PDF."""
        html = self.fetch_page(url_putusan)
        if not html:
            return {}

        soup = BeautifulSoup(html, "html.parser")
        detail_data = {}

        table = soup.find("table")
        if table:
            for tr in table.find_all("tr"):
                tds = tr.find_all("td")
                if len(tds) >= 2:
                    key = tds[0].get_text(strip=True).rstrip(":")
                    val = tds[1].get_text(strip=True)
                    if key:
                        detail_data[key] = val

        pdf_link = soup.find("a", href=lambda h: h and "/pdf/" in h)
        if pdf_link:
            pdf_url = pdf_link["href"]
            if not pdf_url.startswith("http"):
                pdf_url = self.BASE_URL + pdf_url
            detail_data["url_pdf"] = pdf_url

        return detail_data

    def resolve_pdf_url(self, page_url: str) -> Optional[str]:
        """Menemukan URL download PDF dari halaman putusan atau yurisprudensi."""
        html = self.fetch_page(page_url)
        if not html:
            return None

        soup = BeautifulSoup(html, "html.parser")

        # Cek langsung apakah ada link PDF di halaman ini
        pdf_link = soup.find("a", href=lambda h: h and "/pdf/" in h)
        if pdf_link:
            href = pdf_link["href"]
            return href if href.startswith("http") else self.BASE_URL + href

        # Jika halaman yurisprudensi, cari putusan terkait yang pertama
        linked = soup.find("a", href=lambda h: h and "/direktori/putusan/" in h)
        if linked:
            l_href = linked["href"]
            if not l_href.startswith("http"):
                l_href = self.BASE_URL + l_href
            return self.resolve_pdf_url(l_href)

        return None

    def stream_pdf(self, target_url: str, save_to: str) -> bool:
        """Mengunduh stream PDF secara aman dengan bypass Cloudflare dan menyimpannya sementara."""
        pdf_url = target_url
        if not ("/download_file/" in target_url and "/pdf/" in target_url):
            resolved = self.resolve_pdf_url(target_url)
            if not resolved:
                return False
            pdf_url = resolved

        try:
            res = requests.get(pdf_url, impersonate=self.impersonate, timeout=40)
            if res.status_code == 200 and len(res.content) > 100:
                with open(save_to, "wb") as f:
                    f.write(res.content)
                return True
            return False
        except Exception:
            return False

    def extract_pdf_sections(self, pdf_path_or_url: str, temp_pdf_path: str = "") -> Dict:
        """Mengekstrak teks dokumen PDF putusan secara terstruktur (Identitas Pihak, Pertimbangan, Amar)."""
        actual_path = pdf_path_or_url
        if pdf_path_or_url.startswith("http://") or pdf_path_or_url.startswith("https://"):
            if not temp_pdf_path:
                import tempfile
                temp_pdf_path = os.path.join(tempfile.gettempdir(), "temp_ma_extract.pdf")
            ok = self.stream_pdf(pdf_path_or_url, temp_pdf_path)
            if not ok or not os.path.exists(temp_pdf_path):
                return {"success": False, "message": "Gagal mengunduh file PDF dari server MA"}
            actual_path = temp_pdf_path

        try:
            import pypdf
            reader = pypdf.PdfReader(actual_path)
            total_pages = len(reader.pages)
            if total_pages == 0:
                return {"success": False, "message": "File PDF kosong"}

            full_pages_text = []
            for i, p in enumerate(reader.pages):
                txt = p.extract_text() or ""
                full_pages_text.append(txt)

            full_text = "\n--- [HALAMAN BARU] ---\n".join(full_pages_text)

            # Ekstrak bagian awal (halaman 1 s.d. 3) yang memuat identitas para pihak
            first_pages_text = "\n".join(full_pages_text[:min(4, total_pages)])

            # Cari bagian identitas para pihak
            identitas = ""
            pihak_pattern = re.search(
                r'(?:dalam perkara antara|memeriksa perkara.*?antara|melawan)\s*:(.*?)(?:Mahkamah Agung tersebut|Menimbang|DUDUK PERKARA)',
                first_pages_text,
                re.DOTALL | re.IGNORECASE
            )
            if pihak_pattern:
                identitas = pihak_pattern.group(1).strip()
            else:
                identitas = first_pages_text[:2500].strip()

            # Cari bagian amar putusan (biasanya di halaman-halaman terakhir)
            last_pages_text = "\n".join(full_pages_text[max(0, total_pages - 3):])
            amar = ""
            amar_pattern = re.search(
                r'(M\s*E\s*N\s*G\s*A\s*D\s*I\s*L\s*I.*)',
                last_pages_text,
                re.DOTALL | re.IGNORECASE
            )
            if amar_pattern:
                amar = amar_pattern.group(1).strip()
            else:
                amar = last_pages_text[-2500:].strip()

            return {
                "success": True,
                "total_pages": total_pages,
                "identitas_para_pihak": identitas[:4000],
                "amar_putusan": amar[:4000],
                "excerpt": first_pages_text[:3500],
                "full_text_sample": full_text[:12000]
            }
        except Exception as e:
            return {"success": False, "message": f"Gagal mengekstrak teks PDF: {str(e)}"}

    def extract_pagination_info(self, soup: BeautifulSoup, current_page: int) -> Dict:
        """
        Mengekstrak informasi total halaman dan navigasi paginasi dari web MA.
        Menggunakan atribut data-ci-pagination-page untuk menghindari masalah URL
        duplikat yang kadang dihasilkan oleh server MA (misal /page/2/page/499.html).
        """
        max_page = current_page
        pagination = soup.find(class_="pagination")

        if pagination:
            # Prioritas 1: Baca atribut data-ci-pagination-page dari link Last
            last_link = pagination.find("a", string=lambda s: s and "last" in s.lower())
            if last_link:
                ci_page = last_link.get("data-ci-pagination-page", "")
                if ci_page and ci_page.isdigit():
                    max_page = max(max_page, int(ci_page))
                else:
                    # Fallback: ambil angka terakhir dari href (regex greedy dari belakang)
                    href = last_link.get("href", "")
                    matches = re.findall(r'/page/(\d+)', href)
                    if matches:
                        max_page = max(max_page, int(matches[-1]))

            # Prioritas 2: Jika tidak ada Last link, scan angka-angka halaman
            if max_page == current_page:
                for a in pagination.find_all("a"):
                    ci_page = a.get("data-ci-pagination-page", "")
                    if ci_page and ci_page.isdigit():
                        max_page = max(max_page, int(ci_page))
                    else:
                        text = a.get_text(strip=True)
                        if text.isdigit():
                            max_page = max(max_page, int(text))

        return {
            "current_page": current_page,
            "max_page": max(max_page, current_page),
            "has_next": current_page < max_page,
            "has_prev": current_page > 1
        }

    def fetch_yurisprudensi_live(self, max_pages: int = 50) -> List[Dict]:
        """Mengambil SEMUA data kaidah hukum yurisprudensi dari portal resmi Mahkamah Agung (multi-page)."""
        all_items = []

        for page in range(1, max_pages + 1):
            if page == 1:
                url = f"{self.BASE_URL}/yurisprudensi.html"
            else:
                url = f"{self.BASE_URL}/yurisprudensi/index/page/{page}.html"

            html = self.fetch_page(url)
            if not html:
                break

            soup = BeautifulSoup(html, "html.parser")
            page_items = []

            for e in soup.find_all("div", class_="entry-c"):
                link = e.find("a", href=lambda h: h and "/yurisprudensi/detail/" in h)
                if not link:
                    continue
                nomor = link.get_text(strip=True)
                href = link.get("href", "")
                if not href.startswith("http"):
                    href = self.BASE_URL + href

                # Kategori / Kamar
                kamar = "Yurisprudensi Tetap"
                div_small = e.find("div", class_="small")
                if div_small:
                    crumbs = [a.get_text(strip=True) for a in div_small.find_all("a")]
                    if len(crumbs) > 1:
                        kamar = " > ".join(crumbs[1:])

                # Kata kunci
                keywords = ""
                kw_div = e.find(lambda tag: tag.name == "div" and "Kata Kunci" in tag.get_text())
                if kw_div:
                    keywords = kw_div.get_text(strip=True).replace("Kata Kunci :", "").strip()

                # Kaidah hukum
                kaidah_li = e.find("li")
                kaidah = kaidah_li.get_text(strip=True) if kaidah_li else ""

                page_items.append({
                    "nomor": nomor,
                    "kamar": kamar,
                    "url": href,
                    "keywords": keywords,
                    "kaidah": kaidah,
                    "status": "Yurisprudensi Resmi MA"
                })

            if not page_items:
                break  # Tidak ada lagi data di halaman ini

            all_items.extend(page_items)

            # Cek apakah ada halaman berikutnya
            has_next = False
            pagination = soup.find(class_="pagination")
            if pagination:
                next_link = pagination.find("a", string=lambda s: s and ("next" in s.lower() or "»" in s))
                if next_link:
                    has_next = True
                else:
                    # Cek apakah ada link halaman yang lebih besar dari current page
                    for a in pagination.find_all("a"):
                        text = a.get_text(strip=True)
                        if text.isdigit() and int(text) > page:
                            has_next = True
                            break

            if not has_next:
                break

            if page < max_pages:
                time.sleep(self.delay)

        return all_items

    def fetch_direktori_stats(self) -> Dict:
        """Mengambil data counter statistik resmi dari halaman direktori MA secara real-time."""
        url = f"{self.BASE_URL}/direktori.html"
        html = self.fetch_page(url)
        if not html:
            return {}

        soup = BeautifulSoup(html, "html.parser")
        stats = {}
        for a in soup.find_all("a"):
            href = a.get("href", "")
            if "/direktori/index/kategori/" in href:
                slug = href.split("/")[-1].replace(".html", "")
                text = a.get_text(strip=True)
                match = re.search(r'(\d+[\d.]*)$', text)
                if match:
                    num_clean = match.group(1).replace(".", "")
                    stats[slug] = f"{int(num_clean):,}".replace(",", ".")

        return stats

    def extract_sidebar_filters(self, soup: BeautifulSoup) -> List[Dict]:
        """
        Mengekstrak seluruh card filter di sisi kiri (col_one_third) secara dinamis:
        - Card Direktori (Semua Direktori & cabang klasifikasi)
        - Card Klasifikasi / Sub-klasifikasi (misal Perceraian, Waris Islam, dll jika kategori aktif)
        - Card Peradilan / Pengadilan (Daftar pengadilan daerah dengan counter perkara)
        - Card Tahun (Tabel 3 kolom rapi: Putus, Register, Upload)
        """
        col = soup.find("div", class_="col_one_third")
        if not col:
            return []

        cards = []
        for card in col.find_all("div", class_="card"):
            header_el = card.find(class_="card-header")
            if not header_el:
                continue
            title = header_el.get_text(strip=True)

            # Khusus Card Tahun jika berupa tabel 3 kolom (Putus, Register, Upload)
            table = card.find("table")
            if table and "Tahun" in title:
                thead_ths = [th.get_text(strip=True) for th in table.find_all("th")]
                tbody = table.find("tbody")
                col_groups = []
                if tbody:
                    tds = tbody.find_all("td")
                    for idx, td in enumerate(tds):
                        group_name = thead_ths[idx] if idx < len(thead_ths) else f"Kolom {idx+1}"
                        col_items = []
                        for a in td.find_all("a"):
                            href = a.get("href", "")
                            badge_el = a.find(class_=re.compile(r"badge"))
                            count_str = badge_el.get_text(strip=True) if badge_el else ""
                            if badge_el:
                                badge_el.decompose()
                            label = a.get_text(strip=True)
                            if not label or label.lower() == "selengkapnya":
                                continue
                            count_fmt = f"{int(count_str):,}".replace(",", ".") if count_str.isdigit() else count_str

                            m_kat = re.search(r"/kategori/([a-zA-Z0-9\-]+)", href)
                            m_peng = re.search(r"/pengadilan/([a-zA-Z0-9\-]+)", href)
                            m_thn = re.search(r"/tahun/(\d{4})", href)
                            m_per = re.search(r"/tahunjenis/([a-zA-Z0-9\-]+)", href)

                            periode_val = m_per.group(1) if m_per else group_name.lower()
                            tahun_val = int(m_thn.group(1)) if m_thn else (int(label) if label.isdigit() else 0)

                            col_items.append({
                                "label": label,
                                "count": count_fmt,
                                "kategori": m_kat.group(1) if m_kat else "",
                                "peradilan": ("pengadilan/" + m_peng.group(1)) if m_peng and m_peng.group(1) not in ("index", "profil") else "",
                                "tahun": tahun_val,
                                "periode": periode_val,
                                "is_active": "text-success" in a.get("class", [])
                            })
                        col_groups.append({
                            "name": group_name,
                            "periode": group_name.lower(),
                            "items": col_items
                        })

                cards.append({
                    "title": title,
                    "is_table": True,
                    "columns": col_groups
                })
                continue

            # Card standar berformat list (Direktori, Klasifikasi, Pengadilan)
            items = []
            for a in card.find_all("a"):
                href = a.get("href", "")
                full_text = a.get_text(strip=True)
                badge_el = a.find(class_=re.compile(r"badge"))
                count_str = ""
                label = ""
                is_active = "text-success" in a.get("class", [])

                if badge_el:
                    if "badge-success" in badge_el.get("class", []):
                        is_active = True
                    count_str = badge_el.get_text(strip=True)
                    badge_el.decompose()
                    label = a.get_text(strip=True)
                else:
                    m = re.search(r"^(.*?)([\d\.\,]+)$", full_text)
                    if m and len(m.group(2)) >= 4 and not m.group(1).strip().endswith("Tahun"):
                        label = m.group(1).strip()
                        count_str = m.group(2).strip()
                    else:
                        label = full_text

                label = label.strip()
                if not label:
                    continue

                if count_str.isdigit():
                    count_formatted = f"{int(count_str):,}".replace(",", ".")
                else:
                    count_formatted = count_str

                m_kat = re.search(r"/kategori/([a-zA-Z0-9\-]+)", href)
                m_peng = re.search(r"/pengadilan/([a-zA-Z0-9\-]+)", href)
                m_thn = re.search(r"/tahun/(\d{4})", href)
                m_per = re.search(r"/tahunjenis/([a-zA-Z0-9\-]+)", href)

                kategori_slug = m_kat.group(1) if m_kat else ""
                pengadilan_slug = ""
                if m_peng:
                    p_id = m_peng.group(1)
                    if p_id not in ("index", "profil"):
                        pengadilan_slug = "pengadilan/" + p_id
                    else:
                        m_all_p = re.findall(r"/pengadilan/([a-zA-Z0-9\-]+)", href)
                        if m_all_p and m_all_p[-1] not in ("index", "profil"):
                            pengadilan_slug = "pengadilan/" + m_all_p[-1]

                tahun_val = int(m_thn.group(1)) if m_thn else 0
                periode_val = m_per.group(1) if m_per else "putus"

                items.append({
                    "label": label,
                    "count": count_formatted,
                    "kategori": kategori_slug,
                    "peradilan": pengadilan_slug,
                    "tahun": tahun_val,
                    "periode": periode_val,
                    "is_active": is_active
                })

            if items:
                cards.append({"title": title, "is_table": False, "items": items})

        return cards

    def fetch_live_page(
        self,
        kategori: Optional[str] = None,
        peradilan: Optional[str] = None,
        tahun: Optional[int] = None,
        periode: str = "putus",
        page: int = 1,
        query: str = ""
    ) -> Dict:
        """
        Mengambil 1 halaman tertentu secara live tanpa menyimpannya ke disk (Efisien, Cepat, Zero Disk).
        URL selalu dibangun dari base_url internal (tidak mengandalkan href HTML MA yang kadang duplikat).
        """
        is_semua_kat = not kategori or kategori in (None, "", "semua")
        is_semua_peng = not peradilan or peradilan in (None, "", "semua")

        # Jika peradilan spesifik dipilih bersama atau tanpa kategori
        if not is_semua_peng:
            pengadilan_clean = peradilan.replace("pengadilan/", "")
            base_url = f"{self.BASE_URL}/direktori/index/pengadilan/{pengadilan_clean}"
            if not is_semua_kat:
                base_url += f"/kategori/{kategori}"
            if tahun and tahun > 0:
                base_url += f"/tahunjenis/{periode}/tahun/{tahun}"
        elif not is_semua_kat:
            # Klasifikasi perkara (perdata-1, pidana-umum-1, dll)
            base_url = f"{self.BASE_URL}/direktori/index/kategori/{kategori}"
            if tahun and tahun > 0:
                base_url += f"/tahunjenis/{periode}/tahun/{tahun}"
        elif tahun and tahun > 0:
            # Semua klasifikasi, filter tahun saja
            base_url = f"{self.BASE_URL}/direktori/index/tahunjenis/{periode}/tahun/{tahun}"
        else:
            # Semua klasifikasi, semua peradilan, tanpa filter tahun.
            base_url = f"{self.BASE_URL}/direktori/index"

        # Bangun target URL: page 1 gunakan direktori.html jika semua klasifikasi & semua tahun & semua peradilan
        if is_semua_kat and is_semua_peng and not (tahun and tahun > 0):
            if page <= 1:
                target_url = f"{self.BASE_URL}/direktori.html"
            else:
                target_url = f"{self.BASE_URL}/direktori/index/page/{page}.html"
        else:
            if page <= 1:
                target_url = f"{base_url}.html"
            else:
                target_url = f"{base_url}/page/{page}.html"

        if query:
            import urllib.parse
            q_enc = urllib.parse.quote(query)
            sep = "&" if "?" in target_url else "?"
            target_url += f"{sep}q={q_enc}"

        html = self.fetch_page(target_url)
        if not html:
            return {
                "success": False,
                "url": target_url,
                "items": [],
                "sidebar_filters": [],
                "pagination": {"current_page": page, "max_page": 499, "has_next": page < 499, "has_prev": page > 1}
            }

        soup = BeautifulSoup(html, "html.parser")
        items = self.parse_list_page(html)
        pagination_info = self.extract_pagination_info(soup, page)
        sidebar_filters = self.extract_sidebar_filters(soup)

        if pagination_info["max_page"] <= page and page > 1:
            pagination = soup.find(class_="pagination")
            if pagination:
                last_a = pagination.find("a", string=lambda s: s and "last" in s.lower())
                if last_a:
                    ci = last_a.get("data-ci-pagination-page", "")
                    if ci and ci.isdigit() and int(ci) > page:
                        pagination_info["max_page"] = int(ci)
                        pagination_info["has_next"] = page < int(ci)

        return {
            "success": True,
            "url": target_url,
            "items": items,
            "sidebar_filters": sidebar_filters,
            "pagination": pagination_info
        }

    def get_detail_preview(self, url: str) -> Dict:
        """Mengambil ringkasan pratinjau putusan/yurisprudensi (metadata, kaidah, pihak, amar, status PDF) secara instan."""
        html = self.fetch_page(url)
        if not html:
            return {"success": False, "message": "Gagal menghubungi portal Mahkamah Agung"}

        soup = BeautifulSoup(html, "html.parser")
        is_yuris = "/yurisprudensi/" in url

        data = {
            "success": True,
            "url": url,
            "is_yurisprudensi": is_yuris,
            "title": "",
            "metadata": {},
            "kaidah": [],
            "putusan_terkait": [],
            "has_pdf": False,
            "pdf_url": self.resolve_pdf_url(url)
        }
        data["has_pdf"] = bool(data["pdf_url"])

        if is_yuris:
            h1 = soup.find("h1")
            data["title"] = h1.get_text(strip=True) if h1 else "Yurisprudensi MA"
            tbl = soup.find("table")
            if tbl:
                for tr in tbl.find_all("tr"):
                    tds = tr.find_all("td")
                    if len(tds) >= 2:
                        k = tds[0].get_text(strip=True)
                        v = tds[1].get_text(" ", strip=True)
                        if k:
                            data["metadata"][k] = v

            # Kaidah hukum
            for bq in soup.find_all("blockquote"):
                t = bq.get_text(" ", strip=True)
                if t and t not in data["kaidah"]:
                    data["kaidah"].append(t)

            # Ekstrak 'Sumber Putusan' dan 'Putusan Yang Mengikuti' dari panel samping
            col = soup.find(class_="col_one_third")
            sumber_putusan = []
            putusan_mengikuti = []
            if col:
                for card in col.find_all("div", class_="card"):
                    hdr = card.find(class_="card-header")
                    hdr_text = hdr.get_text(strip=True) if hdr else ""
                    if "Sumber Putusan" in hdr_text:
                        for a in card.find_all("a"):
                            href = a.get("href", "")
                            if not href.startswith("http"):
                                href = self.BASE_URL + href
                            sumber_putusan.append({"nomor": a.get_text(strip=True), "href": href})
                    elif "Putusan Yang Mengikuti" in hdr_text:
                        for a in card.find_all("a"):
                            href = a.get("href", "")
                            if not href.startswith("http"):
                                href = self.BASE_URL + href
                            putusan_mengikuti.append({"nomor": a.get_text(strip=True), "href": href})

            data["sumber_putusan"] = sumber_putusan
            data["putusan_mengikuti"] = putusan_mengikuti
            data["putusan_terkait"] = sumber_putusan + putusan_mengikuti
        else:
            tbl = soup.find("table")
            if tbl:
                h2 = tbl.find("h2")
                if h2:
                    data["title"] = h2.get_text(" ", strip=True)
                for tr in tbl.find_all("tr"):
                    tds = tr.find_all("td")
                    if len(tds) == 2:
                        k = tds[0].get_text(strip=True)
                        v = tds[1].get_text(" ", strip=True)
                        if k:
                            data["metadata"][k] = v
            if not data["title"]:
                h1 = soup.find("h1")
                if h1:
                    data["title"] = h1.get_text(" ", strip=True)

        return data

    def scrape_direktori(
        self,
        tahun: Optional[int] = None,
        kategori: Optional[str] = None,
        periode: str = "putus",
        max_pages: int = 1,
        fetch_details: bool = False
    ) -> List[Dict]:
        """Scraping kumpulan halaman (metode batch)."""
        all_data = []
        for p in range(1, max_pages + 1):
            res = self.fetch_live_page(kategori=kategori, tahun=tahun, periode=periode, page=p)
            if not res["items"]:
                break
            all_data.extend(res["items"])
            if p < max_pages:
                time.sleep(self.delay)
        return all_data

    def scrape_tahun(self, tahun: int = 2026, max_pages: int = 1, fetch_details: bool = False) -> List[Dict]:
        return self.scrape_direktori(tahun=tahun, max_pages=max_pages, fetch_details=fetch_details)


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Live Scraper Direktori Putusan Mahkamah Agung RI")
    parser.add_argument("--page", type=int, default=1, help="Halaman spesifik (default: 1)")
    parser.add_argument("--pages", type=int, default=0, help="Jumlah multi halaman jika mode batch")
    parser.add_argument("--tahun", type=int, default=0, help="Tahun perkara (0 untuk semua tahun)")
    parser.add_argument("--kategori", type=str, default="", help="Slug klasifikasi perkara (pidana-umum-1, perdata-agama-1, dll)")
    parser.add_argument("--peradilan", type=str, default="", help="Slug peradilan (pengadilan/mahkamah-agung, pengadilan/pa-jember, dll)")
    parser.add_argument("--periode", type=str, default="putus", choices=["putus", "regis", "upload"])
    parser.add_argument("--q", type=str, default="", help="Kata kunci pencarian live di portal MA")
    parser.add_argument("--live", action="store_true", help="Mode live single page (output cepat JSON)")
    parser.add_argument("--stats", action="store_true", help="Ambil counter statistik direktori MA secara live")
    parser.add_argument("--yurisprudensi", action="store_true", help="Ambil daftar yurisprudensi resmi MA secara live")
    parser.add_argument("--preview", type=str, default="", help="Ambil pratinjau detail putusan/yurisprudensi secara cepat")
    parser.add_argument("--get-pdf", type=str, default="", help="URL halaman putusan/yurisprudensi atau URL download PDF")
    parser.add_argument("--save-to", type=str, default="", help="Path lokal untuk menyimpan stream PDF")
    parser.add_argument("--extract-text", type=str, default="", help="Path lokal atau URL PDF putusan untuk diekstrak teksnya")
    parser.add_argument("--json", action="store_true", help="Output stdout JSON murni")

    args = parser.parse_args()

    scraper = MahkamahAgungScraper(impersonate="chrome", delay=0.8)

    if args.preview:
        preview_data = scraper.get_detail_preview(args.preview)
        sys.stdout.write(json.dumps(preview_data, ensure_ascii=True) + "\n")
    elif args.extract_text:
        extracted = scraper.extract_pdf_sections(args.extract_text, args.save_to)
        sys.stdout.write(json.dumps(extracted, ensure_ascii=True) + "\n")
    elif args.get_pdf and args.save_to:
        ok = scraper.stream_pdf(args.get_pdf, args.save_to)
        sys.stdout.write(json.dumps({"success": ok, "file": args.save_to if ok else ""}) + "\n")
    elif args.yurisprudensi:
        yuris = scraper.fetch_yurisprudensi_live()
        sys.stdout.write(json.dumps(yuris, ensure_ascii=True) + "\n")
    elif args.stats:
        stats = scraper.fetch_direktori_stats()
        sys.stdout.write(json.dumps(stats, ensure_ascii=True) + "\n")
    elif args.live:
        # Live Single-Page Streaming (Paling Cepat & Efisien)
        result = scraper.fetch_live_page(
            kategori=args.kategori.strip() if args.kategori else None,
            peradilan=args.peradilan.strip() if args.peradilan else None,
            tahun=args.tahun if args.tahun > 0 else None,
            periode=args.periode,
            page=args.page,
            query=args.q.strip()
        )
        json_str = json.dumps(result, ensure_ascii=True)
        sys.stdout.write(json_str + "\n")
    else:
        # Batch Mode
        data = scraper.scrape_direktori(
            tahun=args.tahun if args.tahun > 0 else None,
            kategori=args.kategori.strip() if args.kategori else None,
            periode=args.periode,
            max_pages=args.pages or 1
        )
        json_str = json.dumps(data, ensure_ascii=True)
        sys.stdout.write(json_str + "\n")
