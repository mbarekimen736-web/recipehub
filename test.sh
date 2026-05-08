#!/bin/bash

echo "=== TEST RECETTEHUB ===="
echo ""

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Test base de données
echo "1. Test base de données:"
if mysql -u dsi2.1 -pdsi2.1 -h 127.0.0.1 recipehub -e "SELECT 1" &>/dev/null; then
    echo -e "${GREEN}✅ DB OK${NC}"
else
    echo -e "${RED}❌ DB KO${NC}"
fi
echo ""

# 2. Test routes
echo "2. Test routes:"
if php bin/console debug:router | grep -q "recette_index"; then
    echo -e "${GREEN}✅ Routes OK${NC}"
else
    echo -e "${RED}❌ Routes KO${NC}"
fi
echo ""

# 3. Test API
echo "3. Test API:"
API_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/api/recettes)
if [ "$API_RESPONSE" = "200" ]; then
    echo -e "${GREEN}✅ API OK (HTTP $API_RESPONSE)${NC}"
else
    echo -e "${RED}❌ API KO (HTTP $API_RESPONSE)${NC}"
fi
echo ""

# 4. Test dossier upload
echo "4. Test dossier upload:"
if [ -d "public/uploads/recettes" ]; then
    echo -e "${GREEN}✅ Upload dir OK${NC}"
else
    echo -e "${RED}❌ Upload dir KO${NC}"
fi
echo ""

# 5. Test utilisateur
echo "5. Test utilisateur:"
USER_COUNT=$(mysql -u dsi2.1 -pdsi2.1 -h 127.0.0.1 recipehub -e "SELECT COUNT(*) FROM user" 2>/dev/null | tail -1)
if [ "$USER_COUNT" -gt 0 ] 2>/dev/null; then
    echo -e "${GREEN}✅ User OK ($USER_COUNT utilisateurs)${NC}"
else
    echo -e "${RED}❌ User KO${NC}"
fi
echo ""

# 6. Test recettes
echo "6. Test recettes:"
RECETTE_COUNT=$(mysql -u dsi2.1 -pdsi2.1 -h 127.0.0.1 recipehub -e "SELECT COUNT(*) FROM recette" 2>/dev/null | tail -1)
if [ "$RECETTE_COUNT" -gt 0 ] 2>/dev/null; then
    echo -e "${GREEN}✅ Recettes OK ($RECETTE_COUNT recettes)${NC}"
else
    echo -e "${RED}❌ Recettes KO${NC}"
fi
echo ""

# 7. Test API Platform (Swagger)
echo "7. Test Swagger UI:"
SWAGGER_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/api/docs)
if [ "$SWAGGER_RESPONSE" = "200" ]; then
    echo -e "${GREEN}✅ Swagger UI OK (HTTP $SWAGGER_RESPONSE)${NC}"
else
    echo -e "${YELLOW}⚠️ Swagger UI: HTTP $SWAGGER_RESPONSE${NC}"
fi
echo ""

# 8. Test catégories
echo "8. Test catégories:"
CAT_COUNT=$(mysql -u dsi2.1 -pdsi2.1 -h 127.0.0.1 recipehub -e "SELECT COUNT(*) FROM categorie_recette" 2>/dev/null | tail -1)
if [ "$CAT_COUNT" -gt 0 ] 2>/dev/null; then
    echo -e "${GREEN}✅ Catégories OK ($CAT_COUNT catégories)${NC}"
else
    echo -e "${YELLOW}⚠️ Pas de catégories (utile pour les fixtures)${NC}"
fi
echo ""

# 9. Test tags
echo "9. Test tags:"
TAG_COUNT=$(mysql -u dsi2.1 -pdsi2.1 -h 127.0.0.1 recipehub -e "SELECT COUNT(*) FROM tag_recette" 2>/dev/null | tail -1)
if [ "$TAG_COUNT" -gt 0 ] 2>/dev/null; then
    echo -e "${GREEN}✅ Tags OK ($TAG_COUNT tags)${NC}"
else
    echo -e "${YELLOW}⚠️ Pas de tags (utile pour les fixtures)${NC}"
fi
echo ""

# 10. Test fixtures (recommandé)
echo "10. Recommandation:"
if [ "$RECETTE_COUNT" -lt 10 ] 2>/dev/null; then
    echo -e "${YELLOW}⚠️ Vous avez seulement $RECETTE_COUNT recette(s). Pour la partie 8, chargez les fixtures avec:${NC}"
    echo -e "   php bin/console doctrine:fixtures:load"
else
    echo -e "${GREEN}✅ Bon nombre de recettes pour les tests${NC}"
fi

echo ""
echo "=== FIN DES TESTS ==="
