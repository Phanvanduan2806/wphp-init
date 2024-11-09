# Tạo token
https://github.com/settings/tokens/new
```
git clone https://TOKEN@github.com/webangiang/wp-init DOMAIN
```

# Lấy data từ site wordpress
```javascript
const categories = Array.from(document.querySelectorAll('#menu-main a'))
    .map(c => ({title: c?.innerText.trim()}))
    .filter(i => i.title);

const pages = Array.from(document.querySelectorAll('#menu-footer a'))
    .map(p => ({title: p?.innerText.trim()}))
    .filter(i => i.title);

const posts = Array.from(document.querySelectorAll('article'))
    .map(p => {
        const anchor = p.querySelector('a');
        const span = p.querySelector('span[data-bgsrc]');
        const backgroundImage = span ? span.style.backgroundImage : null;
        const thumbnail = backgroundImage
            ? backgroundImage.slice(5, -2) 
            : null;

        return {
            title: anchor ? anchor.getAttribute('title').trim() : null,
            thumbnail: thumbnail
        };
    })
    .filter(i => i.title);

const imgElements = Array.from(document.querySelectorAll('img'))
    .map(i => i.src)
    .filter(Boolean);

const backgroundImages = Array.from(document.querySelectorAll('*'))
    .map(e => e.style.backgroundImage?.slice(5, -2) || window.getComputedStyle(e).backgroundImage?.slice(5, -2))
    .filter(Boolean);

const allImages = Array.from(new Set([...imgElements, ...backgroundImages]));

const output = [
    ...allImages.map(url => [`image, ${url}`]),
    ...categories.map(c => [`category, ${c.title}`]),
    ...pages.map(p => [`page, ${p.title}`]),
    ...posts.map(p => [`post, ${p.title}, ${p.thumbnail || null}`])
];

console.log(output.map(item => item.join(", ")).join("\n"));
```

# Kho theme
http://duan.wuaze.com

# Thông tin account
```
PassFE: P@ssw0rd@@
Link dashboard: https://domain/web_auth
User: seo
Pass: &nXJuz2HTd3j1ZHztihU3kPV
```

# Security
**BẢO MẬT LÀ ƯU TIÊN HÀNG ĐẦU CỦA TEAM DEV VÀ CŨNG LÀ CỦA CTY,** 
**PROJECTS/SẢN PHẨM LÀM RA BỊ HACK ĐÓ LÀ SỰ YẾU KÉM CỦA DEV,** 
**THU NHẬP SẼ BỊ ẢNH HƯỞNG CHỈ VÌ 1 SỰ TẤT TRÁCH NHẤT THỜI**

Try Hard 💪💪💪 and Happy Coding 😉!
_ReadMe sẽ được cập nhật theo issue/sự cố hoặc Policy._
