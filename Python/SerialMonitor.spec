# -*- mode: python ; coding: utf-8 -*-

a = Analysis(
    ["SerialMonitor.py"],
    pathex=[],
    binaries=[],
    datas=[],
    hiddenimports=[],
    hookspath=[],
    hooksconfig={},
    runtime_hooks=[],
    excludes=[],
    noarchive=False,
    optimize=0,
)
pyz = PYZ(a.pure)

exe = EXE(
    pyz,
    a.scripts,
    [],
    exclude_binaries=True,
    name="SerialMonitor",
    debug=False,
    bootloader_ignore_signals=False,
    strip=False,
    upx=True,
    console=False,
    argv_emulation=False,
    target_arch="arm64",
    codesign_identity=None,
    entitlements_file=None,
)
coll = COLLECT(
    exe,
    a.binaries,
    a.datas,
    strip=False,
    upx=True,
    upx_exclude=[],
    name="SerialMonitor",
)
app = BUNDLE(
    coll,
    name="SerialMonitor.app",
    icon=None,
    bundle_identifier="sk.mrsolutions.automationshield.serialmonitor",
    info_plist={
        "CFBundleShortVersionString": "1.0.0",
        "CFBundleVersion": "1",
        "NSHumanReadableCopyright": "Copyright © 2026 MR Solutions",
    },
)
