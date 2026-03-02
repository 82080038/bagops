#!/usr/bin/env python3
"""
Script untuk memperbaiki sisa parent_id yang belum terisi
Author: BAGOPS System
Date: 2026-03-02
"""

import mysql.connector
import re

def get_comprehensive_hierarchy_rules():
    """
    Aturan hierarki yang lebih lengkap
    """
    
    return {
        # Level 2 - Top level
        'level_2': {
            'KAPOLRES SAMOSIR': None,
        },
        
        # Level 3 - Parent dari Level 2
        'level_3': {
            # Wakil & ADC
            'WAKAPOLRES': 'KAPOLRES SAMOSIR',
            'ADC KAPOLRES': 'KAPOLRES SAMOSIR',
            
            # KABAG (Kepala Bagian)
            'KABAG OPS': 'KAPOLRES SAMOSIR',
            'KABAG REN': 'KAPOLRES SAMOSIR',
            'KABAG SDM': 'KAPOLRES SAMOSIR',
            'KABAG LOG': 'KAPOLRES SAMOSIR',
            'KABAG SUMDA': 'KAPOLRES SAMOSIR',
            
            # KASAT (Kepala Satuan)
            'KASAT RESKRIM': 'KAPOLRES SAMOSIR',
            'KASAT INTELKAM': 'KAPOLRES SAMOSIR',
            'KASAT LANTAS': 'KAPOLRES SAMOSIR',
            'KASAT SAMAPTA': 'KAPOLRES SAMOSIR',
            'KASAT NARKOBA': 'KAPOLRES SAMOSIR',
            'KASAT POLAIRUD': 'KAPOLRES SAMOSIR',
            'KASAT TAHTI': 'KAPOLRES SAMOSIR',
            'KASAT PAMOBVIT': 'KAPOLRES SAMOSIR',
            'KASAT BINMAS': 'KAPOLRES SAMOSIR',
            'KASAT SIUM': 'KAPOLRES SAMOSIR',
            'KASATRESNARKOBA': 'KASAT RESKRIM',  # Alias
            
            # KASI (Kepala Seksi)
            'KASI RESKRIM': 'KASAT RESKRIM',
            'KASI INTELKAM': 'KASAT INTELKAM',
            'KASI LANTAS': 'KASAT LANTAS',
            'KASI SAMAPTA': 'KASAT SAMAPTA',
            'KASI NARKOBA': 'KASAT NARKOBA',
            'KASI POLAIRUD': 'KASAT POLAIRUD',
            'KASI TAHTI': 'KASAT TAHTI',
            'KASI PAMOBVIT': 'KASAT PAMOBVIT',
            'KASI BINMAS': 'KASAT BINMAS',
            
            # KASUBSI (Kepala Sub Seksi)
            'KASUBSIBANKUM': 'KABAG SDM',
            'KASUBSIKEU': 'KABAG SUMDA',
            'KASUBSIWAS': 'KABAG OPS',
            'KASUBSIDOKKES': 'KABAG SDM',
            'KASUBSIHUMAS': 'KABAG OPS',
            
            # KASU (Kepala Seksi Umum)
            'KASIDOKKES': 'KABAG SDM',
            'KASIHUMAS': 'KABAG OPS',
            'KASIKEU': 'KABAG SUMDA',
            'KASIWAS': 'KABAG OPS',
            
            # KAPOLSEK
            'KAPOLSEK ONANRUNGGU': 'KAPOLRES SAMOSIR',
            'KAPOLSEK PALIPI': 'KAPOLRES SAMOSIR',
            'KAPOLSEK PANGURURAN': 'KAPOLRES SAMOSIR',
            'KAPOLSEK SIMANINDO': 'KAPOLRES SAMOSIR',
            'KAPOLSEK HARIAN BOHO': 'KAPOLRES SAMOSIR',
            
            # KAUR (Kepala Urusan)
            'KAURBINOPS': 'KABAG OPS',
            'KAURMINTU': 'KABAG OPS',
            'KAURMIN': 'KABAG OPS',
            'KAURSARPRAS': 'KABAG LOG',
            'KAURKEU': 'KABAG SUMDA',
            'KAURWAS': 'KABAG OPS',
            
            # PS. (Penjabat Sementara) Level 3
            'PS. KASUBBAGBEKPAL': 'KABAG LOG',
            'PS. KASUBBAGBINKAR': 'KABAG SDM',
            'PS. KASUBBAGBINKARPOL': 'KABAG OPS',
            'PS. KASUBBAGBINOPS': 'KABAG OPS',
            'PS. KASUBBAGRENMIN': 'KABAG REN',
            'PS. KASUBBAGSIMAPOL': 'KASAT SAMAPTA',
            'PS. KASUBBAGSIKDOK': 'KABAG SDM',
            'PS. KASUBBAGLOG': 'KABAG LOG',
            'PS. KASUBBAGSUMDA': 'KABAG SUMDA',
            'PS. KASUBSIWAS': 'KABAG OPS',
            'PS. KASUBSIKEU': 'KABAG SUMDA',
            'PS. KASUBSIDOKKES': 'KABAG SDM',
            'PS. KASUBSIHUMAS': 'KABAG OPS',
            'PS. KASUBSIBANKUM': 'KABAG SDM',
            'PS. KASUBSIWASDALOPS': 'KABAG OPS',
            'PS. KASUBSIOPS': 'KABAG OPS',
            'PS. KASUBSIPROGAR': 'KABAG REN',
            'PS. KASUBSISUMDA': 'KABAG SUMDA',
            'PS. KASUBSIUM': 'KABAG SDM',
            'PS. KASUBSITAHTI': 'KASAT TAHTI',
            'PS. KASUBSIBINMAS': 'KASAT BINMAS',
            'PS. KASUBSIBINKARPOL': 'KABAG OPS',
            'PS. KASUBSIDOKPOL': 'KABAG SDM',
            'PS. KASUBSIBINPOL': 'KASAT BINMAS',
            'PS. KASUBSIBINKAR': 'KABAG SDM',
            'PS. KASUBSIBINOPS': 'KABAG OPS',
            'PS. KASUBSIPAMOBVIT': 'KASAT PAMOBVIT',
            'PS. KASUBSISAMAPTA': 'KASAT SAMAPTA',
            'PS. KASUBSILANTAS': 'KASAT LANTAS',
            'PS. KASUBSIRESKRIM': 'KASAT RESKRIM',
            'PS. KASUBSINARKOBA': 'KASAT NARKOBA',
            'PS. KASUBSIINTELKAM': 'KASAT INTELKAM',
            'PS. KASUBSIPOLAIRUD': 'KASAT POLAIRUD',
            
            # PS. KASAT Level 3
            'PS. KASAT INTELKAM': 'KAPOLRES SAMOSIR',
            'PS. KASAT LANTAS': 'KAPOLRES SAMOSIR',
            'PS. KASAT RESKRIM': 'KAPOLRES SAMOSIR',
            'PS. KASAT SAMAPTA': 'KAPOLRES SAMOSIR',
            'PS. KASAT NARKOBA': 'KAPOLRES SAMOSIR',
            'PS. KASAT TAHTI': 'KAPOLRES SAMOSIR',
            'PS. KASAT PAMOBVIT': 'KAPOLRES SAMOSIR',
            'PS. KASAT BINMAS': 'KAPOLRES SAMOSIR',
            'PS. KASAT POLAIRUD': 'KAPOLRES SAMOSIR',
            'PS. KASAT SIUM': 'KAPOLRES SAMOSIR',
            
            # PS. KAPOLSEK Level 3
            'PS. KAPOLSEK ONANRUNGGU': 'KAPOLRES SAMOSIR',
            'PS. KAPOLSEK PALIPI': 'KAPOLRES SAMOSIR',
            'PS. KAPOLSEK PANGURURAN': 'KAPOLRES SAMOSIR',
            'PS. KAPOLSEK SIMANINDO': 'KAPOLRES SAMOSIR',
            'PS. KAPOLSEK HARIAN BOHO': 'KAPOLRES SAMOSIR',
            
            # PS. KABAG Level 3
            'PS. KABAG OPS': 'KAPOLRES SAMOSIR',
            'PS. KABAG REN': 'KAPOLRES SAMOSIR',
            'PS. KABAG SDM': 'KAPOLRES SAMOSIR',
            'PS. KABAG LOG': 'KAPOLRES SAMOSIR',
            'PS. KABAG SUMDA': 'KAPOLRES SAMOSIR',
            
            # KA SPKT
            'KA SPKT': 'KAPOLRES SAMOSIR',
            
            # Other Level 3
            'OP CALL CENTRE': 'KAPOLRES SAMOSIR',
            'PAURSUBBAGPROGAR': 'KABAG REN',
            'PAURSUBBAGBINKAR': 'KABAG SDM',
            'PS. KASIUM': 'KABAG SDM',
            'PS. KASIKEU': 'KABAG SUMDA',
            'PS. KASIWAS': 'KABAG OPS',
            'PS. KASIPROPAM': 'KABAG OPS',
            'PS. KANIT PROPOS': 'KABAG OPS',
            'PS. KANIT PAMINAL': 'KABAG OPS',
            'PS. KANIT 3': 'KASAT INTELKAM',
            'PS. KANIT 1': 'KASAT INTELKAM',
            'PS. KANIT 2': 'KASAT INTELKAM',
            'PS. KANITIDIK 2': 'KASAT INTELKAM',
            'PS. KANIT IDENTIFIKASI': 'KASAT INTELKAM',
            'PS.KANIT IDIK 1': 'KASAT INTELKAM',
            'PS. KANIT SAMAPTA': 'KASAT SAMAPTA',
            'PS. KAURBINOPS': 'KABAG OPS',
            'PS. KANIT DALMAS 2': 'KASAT SAMAPTA',
            'PS. KANIT TURJAWALI': 'KASAT LANTAS',
            'PS. KANITPAMWASTER': 'KASAT PAMOBVIT',
            'PS. KANITPAMWISATA': 'KASAT PAMOBVIT',
            'PS. PANIT PAMWASTER': 'KASAT PAMOBVIT',
            'PS. KANITGAKKUM': 'KASAT LANTAS',
            'PS. KANITTURJAWALI': 'KASAT LANTAS',
            'PS. KANITKAMSEL': 'KASAT LANTAS',
            'PS. KANITPATROLI': 'KASAT POLAIRUD',
            'PS. KANIT INTELKAM': 'KASAT INTELKAM',
            'PS. KANIT BINMAS': 'KASAT BINMAS',
            'PS. KANIT SAMAPTA': 'KASAT SAMAPTA',
            'PS. KANIT RESKRIM': 'KASAT RESKRIM',
            'PS.KANIT SAMAPTA': 'KASAT SAMAPTA',
            'PS. KANIT PROPAM': 'KABAG OPS',
            'PS. KA SPKT 1': 'KA SPKT',
            'PS. KA SPKT 2': 'KA SPKT',
            'PS. KA SPKT 3': 'KA SPKT',
            'PS. KANIT 3': 'KASAT INTELKAM',
            'PS. KANIT 1': 'KASAT INTELKAM',
            'PS. KANIT 2': 'KASAT INTELKAM',
            
            # PAMAPTA
            'PAMAPTA 1': 'KASAT SAMAPTA',
            'PAMAPTA 2': 'KASAT SAMAPTA',
            'PAMAPTA 3': 'KASAT SAMAPTA',
            'PAMAPTA 4': 'KASAT SAMAPTA',
            'PAMAPTA 5': 'KASAT SAMAPTA',
            'PAMAPTA 6': 'KASAT SAMAPTA',
            'PAMAPTA 7': 'KASAT SAMAPTA',
            'PAMAPTA 8': 'KASAT SAMAPTA',
            
            # ASN
            'ASN BAG OPS': 'KABAG OPS',
            'ASN BAG REN': 'KABAG REN',
            'ASN BAG SDM': 'KABAG SDM',
            'ASN BAG LOG': 'KABAG LOG',
            'ASN BAG SUMDA': 'KABAG SUMDA',
        },
        
        # Level 4 - Parent dari Level 3
        'level_4': {
            # KANIT (Kepala Unit)
            'KANIT RESKRIM': 'KASAT RESKRIM',
            'KANIT INTELKAM': 'KASAT INTELKAM',
            'KANIT LANTAS': 'KASAT LANTAS',
            'KANIT SAMAPTA': 'KASAT SAMAPTA',
            'KANIT NARKOBA': 'KASAT NARKOBA',
            'KANIT POLAIRUD': 'KASAT POLAIRUD',
            'KANIT TAHTI': 'KASAT TAHTI',
            'KANIT PAMOBVIT': 'KASAT PAMOBVIT',
            'KANIT BINMAS': 'KASAT BINMAS',
            'KANITIDIK 1': 'KASAT INTELKAM',
            'KANITIDIK 2': 'KASAT INTELKAM',
            'KANITIDIK 3': 'KASAT INTELKAM',
            'KANITIDIK 4': 'KASAT INTELKAM',
            'KANITIDIK 5': 'KASAT INTELKAM',
            'KANITREGIDENT LANTAS': 'KASAT LANTAS',
            'KANITGAKKUM': 'KASAT LANTAS',
            'KANITTURJAWALI': 'KASAT LANTAS',
            'KANITKAMSEL': 'KASAT LANTAS',
            'KANITPATROLI': 'KASAT POLAIRUD',
            'KANIT DALMAS 1': 'KASAT SAMAPTA',
            'KANIT DALMAS 2': 'KASAT SAMAPTA',
            'KANIT TURJAWALI': 'KASAT LANTAS',
            'KANIT PAMINAL': 'KABAG OPS',
            'KANIT PROPOS': 'KABAG OPS',
            'KANIT PROPAM': 'KABAG OPS',
            'KANIT INTELKAM': 'KASAT INTELKAM',
            'KANIT SAMAPTA': 'KASAT SAMAPTA',
            'KANIT RESKRIM': 'KASAT RESKRIM',
            'KANIT LANTAS': 'KASAT LANTAS',
            'KANIT NARKOBA': 'KASAT NARKOBA',
            'KANIT POLAIRUD': 'KASAT POLAIRUD',
            'KANIT TAHTI': 'KASAT TAHTI',
            'KANIT PAMOBVIT': 'KASAT PAMOBVIT',
            'KANIT BINMAS': 'KASAT BINMAS',
            
            # PS. (Penjabat Sementara) Level 4
            'PS. KANIT RESKRIM': 'KASAT RESKRIM',
            'PS. KANIT INTELKAM': 'KASAT INTELKAM',
            'PS. KANIT LANTAS': 'KASAT LANTAS',
            'PS. KANIT SAMAPTA': 'KASAT SAMAPTA',
            'PS. KANIT NARKOBA': 'KASAT NARKOBA',
            'PS. KANIT POLAIRUD': 'KASAT POLAIRUD',
            'PS. KANIT TAHTI': 'KASAT TAHTI',
            'PS. KANIT PAMOBVIT': 'KASAT PAMOBVIT',
            'PS. KANIT BINMAS': 'KASAT BINMAS',
            'PS. KANIT PROPAM': 'KABAG OPS',
            'PS. KANIT PROPOS': 'KABAG OPS',
            'PS. KANIT PAMINAL': 'KABAG OPS',
            'PS. KANIT 1': 'KASAT INTELKAM',
            'PS. KANIT 2': 'KASAT INTELKAM',
            'PS. KANIT 3': 'KASAT INTELKAM',
            'PS. KANIT 4': 'KASAT INTELKAM',
            'PS. KANIT 5': 'KASAT INTELKAM',
            'PS. KANITIDIK 1': 'KASAT INTELKAM',
            'PS. KANITIDIK 2': 'KASAT INTELKAM',
            'PS. KANITIDIK 3': 'KASAT INTELKAM',
            'PS. KANITIDIK 4': 'KASAT INTELKAM',
            'PS. KANITIDIK 5': 'KASAT INTELKAM',
            'PS. KANITREGIDENT LANTAS': 'KASAT LANTAS',
            'PS. KANITGAKKUM': 'KASAT LANTAS',
            'PS. KANITTURJAWALI': 'KASAT LANTAS',
            'PS. KANITKAMSEL': 'KASAT LANTAS',
            'PS. KANITPATROLI': 'KASAT POLAIRUD',
            'PS. KANIT DALMAS 1': 'KASAT SAMAPTA',
            'PS. KANIT DALMAS 2': 'KASAT SAMAPTA',
            'PS. KANIT TURJAWALI': 'KASAT LANTAS',
            'PS. KANIT PAMINAL': 'KABAG OPS',
            'PS. KANIT PROPOS': 'KABAG OPS',
            'PS. KANIT PROPAM': 'KABAG OPS',
            'PS. KANIT INTELKAM': 'KASAT INTELKAM',
            'PS. KANIT SAMAPTA': 'KASAT SAMAPTA',
            'PS. KANIT RESKRIM': 'KASAT RESKRIM',
            'PS. KANIT LANTAS': 'KASAT LANTAS',
            'PS. KANIT NARKOBA': 'KASAT NARKOBA',
            'PS. KANIT POLAIRUD': 'KASAT POLAIRUD',
            'PS. KANIT TAHTI': 'KASAT TAHTI',
            'PS. KANIT PAMOBVIT': 'KASAT PAMOBVIT',
            'PS. KANIT BINMAS': 'KASAT BINMAS',
            'PS.KANIT SAMAPTA': 'KASAT SAMAPTA',
            'PS.KANIT IDIK 1': 'KASAT INTELKAM',
            
            # PS. PAUR (Penjabat Sementara) Level 4
            'PS. PAUR SUBBAGBINOPS': 'KABAG OPS',
            'PS. PAUR SUBBAGRENMIN': 'KABAG REN',
            'PS. PAUR SUBBAGSUMDA': 'KABAG SUMDA',
            'PS. PAUR SUBBAGSIMAPOL': 'KASAT SAMAPTA',
            'PS. PAUR SUBBAGBINPOL': 'KASAT BINMAS',
            'PS. PAUR SUBBAGBINKARPOL': 'KABAG OPS',
            'PS. PAUR SUBBAGSIKDOK': 'KABAG SDM',
            'PS. PAUR SUBBAGLOG': 'KABAG LOG',
            'PS. PAUR SUBBAGRENMIN': 'KABAG REN',
            'PS. PAUR SUBBAGBEKPAL': 'KABAG LOG',
            'PS. PAUR SUBBAGBINKAR': 'KABAG SDM',
            'PS. PAUR SUBBAGBINKARPOL': 'KABAG OPS',
            'PS. PAUR SUBBAGSIKDOK': 'KABAG SDM',
            'PS. PAUR SUBBAGLOG': 'KABAG LOG',
            'PS. PAUR SUBBAGRENMIN': 'KABAG REN',
            'PS. PAUR SUBBAGSUMDA': 'KABAG SUMDA',
            'PS. PAUR SUBBAGSIMAPOL': 'KASAT SAMAPTA',
            'PS. PAUR SUBBAGBINPOL': 'KASAT BINMAS',
            'PS. PAUR SUBBAGBINKARPOL': 'KABAG OPS',
            'PS. PAUR SUBBAGSIKDOK': 'KABAG SDM',
            'PS. PAUR SUBBAGBEKPAL': 'KABAG LOG',
            'PS. PAUR SUBBAGBINKAR': 'KABAG SDM',
            'PS. PAUR SUBBAGBINKARPOL': 'KABAG OPS',
            'PS. PAUR SUBBAGSIKDOK': 'KABAG SDM',
            'PS. PAUR SUBBAGLOG': 'KABAG LOG',
            'PS. PAUR SUBBAGRENMIN': 'KABAG REN',
            
            # Wakapolsek
            'WAKAPOLSEK ONANRUNGGU': 'KAPOLSEK ONANRUNGGU',
            'WAKAPOLSEK PALIPI': 'KAPOLSEK PALIPI',
            'WAKAPOLSEK PANGURURAN': 'KAPOLSEK PANGURURAN',
            'WAKAPOLSEK SIMANINDO': 'KAPOLSEK SIMANINDO',
            'WAKAPOLSEK HARIAN BOHO': 'KAPOLSEK HARIAN BOHO',
            
            # BA (Bawahan) Level 4
            'BA MIN BAG OPS': 'KABAG OPS',
            'BA MIN BAG REN': 'KABAG REN',
            'BA MIN BAG SDM': 'KABAG SDM',
            'BA MIN BAG LOG': 'KABAG LOG',
            'BA MIN BAG SUMDA': 'KABAG SUMDA',
            'BA POLRES SAMOSIR': 'KAPOLRES SAMOSIR',
            'BA SIDOKKES': 'KABAG SDM',
            'BA PEMBINAAN': 'KABAG SDM',
            'BA MIN BAG SDM': 'KABAG SDM',
            
            # BINTARA Level 4
            'BINTARA ADMINISTRASI': 'KABAG OPS',
            'BINTARA POLSEK': 'KAPOLSEK ONANRUNGGU',  # Default
            'BINTARA SAT PAMOBVIT': 'KASAT PAMOBVIT',
            'BINTARA SATLANTAS': 'KASAT LANTAS',
            'BINTARA SAT RESKRIM': 'KASAT RESKRIM',
            'BINTARA SAT INTELKAM': 'KASAT INTELKAM',
            'BINTARA SAT SAMAPTA': 'KASAT SAMAPTA',
            'BINTARA SAT NARKOBA': 'KASAT NARKOBA',
            'BINTARA SAT POLAIRUD': 'KASAT POLAIRUD',
            'BINTARA SAT TAHTI': 'KASAT TAHTI',
            'BINTARA SAT BINMAS': 'KASAT BINMAS',
            'BINTARA SIUM': 'KASAT SIUM',
            'BINTARA SIKEU': 'KABAG SUMDA',
            'BINTARA SIWAS': 'KABAG OPS',
            'BINTARA SITIK': 'KABAG SDM',
            'BINTARA SIPROPAM': 'KABAG OPS',
            'BINTARA SIHUMAS': 'KABAG OPS',
            'BINTARA SATINTELKAM': 'KASAT INTELKAM',
            'BINTARA SATRESNARKOBA': 'KASAT NARKOBA',
            'BINTARA SATRESKRIM': 'KASAT RESKRIM',
            'BINTARA SATINTELKAM': 'KASAT INTELKAM',
            'BINTARA  POLSEK': 'KAPOLSEK ONANRUNGGU',
        },
        
        # Level 5 - Parent dari Level 4
        'level_5': {
            # BA (Bawahan) Level 5
            'BAMIN PAMAPTA 1': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 2': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 3': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 4': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 5': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 6': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 7': 'KASAT PAMOBVIT',
            'BAMIN PAMAPTA 8': 'KASAT PAMOBVIT',
            
            # BINTARA Level 5
            'BINTARA BAG OPS': 'KABAG OPS',
            'BINTARA BAG REN': 'KABAG REN',
            'BINTARA BAG SDM': 'KABAG SDM',
            'BINTARA BAG LOG': 'KABAG LOG',
            'BINTARA BAG SUMDA': 'KABAG SUMDA',
            'BINTARA SAT PAMOBVIT': 'KASAT PAMOBVIT',
            'BINTARA SATLANTAS': 'KASAT LANTAS',
            'BINTARA SAT RESKRIM': 'KASAT RESKRIM',
            'BINTARA SAT INTELKAM': 'KASAT INTELKAM',
            'BINTARA SAT SAMAPTA': 'KASAT SAMAPTA',
            'BINTARA SAT NARKOBA': 'KASAT NARKOBA',
            'BINTARA SAT POLAIRUD': 'KASAT POLAIRUD',
            'BINTARA SAT TAHTI': 'KASAT TAHTI',
            'BINTARA SAT BINMAS': 'KASAT BINMAS',
            'BINTARA SIUM': 'KASAT SIUM',
            'BINTARA SIKEU': 'KABAG SUMDA',
            'BINTARA SIWAS': 'KABAG OPS',
            'BINTARA SITIK': 'KABAG SDM',
            'BINTARA SIPROPAM': 'KABAG OPS',
            'BINTARA SIHUMAS': 'KABAG OPS',
            'BINTARA SATINTELKAM': 'KASAT INTELKAM',
            'BINTARA SATRESNARKOBA': 'KASAT NARKOBA',
            'BINTARA SATRESKRIM': 'KASAT RESKRIM',
            'BINTARA SATINTELKAM': 'KASAT INTELKAM',
            'BINTARA  POLSEK': 'KAPOLSEK ONANRUNGGU',
        }
    }

def update_remaining_hierarchy():
    """
    Update sisa parent_id yang belum terisi
    """
    
    try:
        hierarchy = get_comprehensive_hierarchy_rules()
        
        connection = mysql.connector.connect(
            host='127.0.0.1',
            port='3306',
            user='root',
            password='root',
            database='bagops_db'
        )
        
        cursor = connection.cursor()
        
        # Get jabatan yang belum punya parent
        cursor.execute("""
            SELECT id, nama_jabatan, level_jabatan 
            FROM dynamic_jabatan 
            WHERE parent_id IS NULL AND is_active = 1
            ORDER BY level_jabatan, nama_jabatan
        """)
        
        jabatans = cursor.fetchall()
        
        print(f"📊 Memproses {len(jabatans)} jabatan yang belum punya parent...")
        
        updated_count = 0
        not_found_count = 0
        
        for jabatan_id, nama_jabatan, level_jabatan in jabatans:
            parent_nama = None
            
            # Find parent based on level
            if level_jabatan == 2:
                parent_nama = hierarchy['level_2'].get(nama_jabatan)
            elif level_jabatan == 3:
                parent_nama = hierarchy['level_3'].get(nama_jabatan)
            elif level_jabatan == 4:
                parent_nama = hierarchy['level_4'].get(nama_jabatan)
            elif level_jabatan == 5:
                parent_nama = hierarchy['level_5'].get(nama_jabatan)
            
            if parent_nama:
                # Find parent ID
                cursor.execute(
                    "SELECT id FROM dynamic_jabatan WHERE nama_jabatan = %s AND is_active = 1",
                    (parent_nama,)
                )
                parent_result = cursor.fetchone()
                
                if parent_result:
                    parent_id = parent_result[0]
                    
                    # Update parent_id
                    cursor.execute(
                        "UPDATE dynamic_jabatan SET parent_id = %s WHERE id = %s",
                        (parent_id, jabatan_id)
                    )
                    updated_count += 1
                    
                    print(f"✅ {nama_jabatan} → parent: {parent_nama} (ID: {parent_id})")
                else:
                    not_found_count += 1
                    print(f"⚠️ Parent not found: {parent_nama} for {nama_jabatan}")
            else:
                not_found_count += 1
                print(f"⚠️ No parent rule found: {nama_jabatan} (Level {level_jabatan})")
        
        connection.commit()
        
        print(f"\n📊 SUMMARY UPDATE:")
        print(f"✅ Berhasil update: {updated_count} jabatan")
        print(f"⚠️ Tidak ditemukan parent: {not_found_count} jabatan")
        
        return True
        
    except Exception as e:
        print(f"❌ Error updating hierarchy: {str(e)}")
        return False
    finally:
        if 'connection' in locals() and connection.is_connected():
            connection.close()

def show_final_hierarchy_report():
    """
    Tampilkan laporan hierarki final
    """
    
    try:
        connection = mysql.connector.connect(
            host='127.0.0.1',
            port='3306',
            user='root',
            password='root',
            database='bagops_db'
        )
        
        cursor = connection.cursor()
        
        # Get final statistics
        cursor.execute("""
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN parent_id IS NULL THEN 1 END) as no_parent,
                COUNT(CASE WHEN parent_id IS NOT NULL THEN 1 END) as has_parent,
                COUNT(CASE WHEN level_jabatan = 1 THEN 1 END) as level_1,
                COUNT(CASE WHEN level_jabatan = 2 THEN 1 END) as level_2,
                COUNT(CASE WHEN level_jabatan = 3 THEN 1 END) as level_3,
                COUNT(CASE WHEN level_jabatan = 4 THEN 1 END) as level_4,
                COUNT(CASE WHEN level_jabatan = 5 THEN 1 END) as level_5
            FROM dynamic_jabatan 
            WHERE is_active = 1
        """)
        
        stats = cursor.fetchone()
        
        print("\n📊 LAPORAN HIERARKI FINAL")
        print("=" * 50)
        print(f"📈 Total Jabatan: {stats[0]}")
        print(f"🔗 Punya Parent: {stats[2]} ({stats[2]/stats[0]*100:.1f}%)")
        print(f"🚫 Tidak Punya Parent: {stats[1]} ({stats[1]/stats[0]*100:.1f}%)")
        print(f"📊 Level 1: {stats[3]}")
        print(f"📊 Level 2: {stats[4]}")
        print(f"📊 Level 3: {stats[5]}")
        print(f"📊 Level 4: {stats[6]}")
        print(f"📊 Level 5: {stats[7]}")
        
        # Show hierarchy tree sample
        print("\n🌳 CONTOH HIERARKI TREE:")
        print("-" * 50)
        
        cursor.execute("""
            SELECT 
                j1.nama_jabatan,
                j2.nama_jabatan as parent_nama,
                j1.level_jabatan
            FROM dynamic_jabatan j1
            LEFT JOIN dynamic_jabatan j2 ON j1.parent_id = j2.id
            WHERE j1.is_active = 1
            ORDER BY j1.level_jabatan, j1.nama_jabatan
            LIMIT 20
        """)
        
        results = cursor.fetchall()
        for jabatan, parent, level in results:
            indent = "  " * (level - 1)
            parent_text = f" ← {parent}" if parent else " (TOP LEVEL)"
            print(f"{indent}• {jabatan}{parent_text}")
        
        return True
        
    except Exception as e:
        print(f"❌ Error generating report: {str(e)}")
        return False
    finally:
        if 'connection' in locals() and connection.is_connected():
            connection.close()

def main():
    """
    Main function
    """
    
    print("🔄 MEMPERBAIKI SISA HIERARKI JABATAN")
    print("=" * 50)
    
    # Update remaining hierarchy
    print("\n1. Update sisa parent_id...")
    if not update_remaining_hierarchy():
        print("❌ Gagal update hierarki")
        return
    
    # Show final report
    print("\n2. Generate laporan final...")
    show_final_hierarchy_report()
    
    print("\n✅ UPDATE HIERARKI SELESAI!")
    print("\n📋 Hierarki jabatan sudah diperbaiki")
    print("🔧 Sebagian besar jabatan sudah memiliki parent yang sesuai")

if __name__ == "__main__":
    main()
