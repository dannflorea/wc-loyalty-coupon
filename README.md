# WC Loyalty Coupon

Plugin WooCommerce care generează automat cupoane de fidelitate când o comandă atinge un prag configurat.

## Instalare

1. Copiază folderul `wc-loyalty-coupon` în `/wp-content/plugins/`
2. Activează pluginul din **Plugins → Plugins instalate**
3. Configurează din **WooCommerce → Setări → Avansat → Cupon Fidelitate**

## Setări disponibile (toate din admin)

| Setare | Descriere | Default |
|---|---|---|
| Activat | On/Off rapid | Da |
| Prag comandă (lei) | Valoarea minimă a coșului (subtotal, fără livrare) | 500 |
| Tip reducere | Procent / Sumă fixă / Transport gratuit | Procent |
| Valoare reducere | % sau lei, în funcție de tip | 10 |
| Expirare (zile) | 0 = fără expirare | 30 |
| Mesaj coș (sub prag) | Suportă `{amount}` și `{threshold}` | Vezi default |
| Mesaj coș (prag atins) | Afișat când subtotalul ≥ prag | Vezi default |

## Flow

1. Client adaugă produse în coș → mesaj cu progress bar
2. Subtotal ≥ prag → mesaj de felicitare
3. Comanda → **Completed**
4. Plugin generează cupon (`FIDEL-XXXXXXXXXX`):
   - legat de emailul clientului
   - usage limit = 1
   - expirare configurabilă
5. Cuponul apare vizual în emailul standard WooCommerce "Comandă finalizată"

## Note tehnice

- Generarea se face o singură dată per comandă (protecție anti-duplicat via `_wlc_coupon_generated` meta)
- Emailul cu cuponul este emailul nativ WooCommerce "Customer Completed Order" — nu se trimite un email separat
- Cuponul este restricționat la emailul de billing al clientului
- `individual_use = true` — nu se combină cu alte cupoane
