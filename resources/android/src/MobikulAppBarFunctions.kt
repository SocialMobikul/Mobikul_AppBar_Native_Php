package com.mobikul.plugins.appbar

import androidx.fragment.app.FragmentActivity
import com.nativephp.mobile.bridge.BridgeFunction
import com.nativephp.mobile.bridge.BridgeResponse

object MobikulAppBarFunctions {
    private val allowedIcons = setOf("back", "menu", "search", "cart", "profile", "wishlist", "share", "filter", "notifications", "more")

    class Configure(private val activity: FragmentActivity) : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            val title = (parameters["title"] as? String)?.trim().orEmpty().ifEmpty { "App" }
            val context = (parameters["context"] as? String)?.trim().orEmpty().ifEmpty { "default" }
            val icons = normalizeIcons(parameters["icons"], context)
            val options = normalizeOptions(parameters["options"])

            return BridgeResponse.success(
                mapOf(
                    "title" to title,
                    "context" to context,
                    "icons" to icons,
                    "options" to options,
                    "updated" to true
                )
            )
        }
    }

    class Reset(private val activity: FragmentActivity) : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            return BridgeResponse.success(
                mapOf(
                    "title" to "App",
                    "context" to "default",
                    "icons" to defaultIcons("default"),
                    "options" to defaultOptions(),
                    "updated" to true
                )
            )
        }
    }

    private fun normalizeIcons(rawIcons: Any?, context: String): List<Map<String, String>> {
        val normalized = mutableListOf<Map<String, String>>()

        if (rawIcons is List<*>) {
            rawIcons.forEach { item ->
                val map = item as? Map<*, *> ?: return@forEach
                val icon = (map["icon"] as? String)?.trim().orEmpty()
                val action = (map["action"] as? String)?.trim().orEmpty()

                if (icon.isNotEmpty() && action.isNotEmpty() && allowedIcons.contains(icon)) {
                    normalized.add(mapOf("icon" to icon, "action" to action))
                }
            }
        }

        return if (normalized.isEmpty()) defaultIcons(context) else normalized
    }

    private fun defaultIcons(context: String): List<Map<String, String>> {
        return when (context.lowercase()) {
            "home" -> listOf(mapOf("icon" to "menu", "action" to "open_menu"), mapOf("icon" to "search", "action" to "open_search"), mapOf("icon" to "cart", "action" to "open_cart"))
            "product" -> listOf(mapOf("icon" to "back", "action" to "go_back"), mapOf("icon" to "wishlist", "action" to "add_to_wishlist"), mapOf("icon" to "share", "action" to "share_product"))
            "checkout" -> listOf(mapOf("icon" to "back", "action" to "go_back"), mapOf("icon" to "cart", "action" to "open_cart"))
            "profile" -> listOf(mapOf("icon" to "back", "action" to "go_back"), mapOf("icon" to "notifications", "action" to "open_notifications"), mapOf("icon" to "more", "action" to "open_more"))
            else -> listOf(mapOf("icon" to "menu", "action" to "open_menu"), mapOf("icon" to "search", "action" to "open_search"))
        }
    }

    private fun normalizeOptions(rawOptions: Any?): Map<String, Any?> {
        val options = rawOptions as? Map<*, *> ?: emptyMap<Any, Any>()
        val defaults = defaultOptions()

        return mapOf(
            "showAppLogo" to booleanOption(options, "showAppLogo", defaults["showAppLogo"] as Boolean),
            "isElevated" to booleanOption(options, "isElevated", defaults["isElevated"] as Boolean),
            "isLeadingEnable" to booleanOption(options, "isLeadingEnable", defaults["isLeadingEnable"] as Boolean),
            "isHomeEnable" to booleanOption(options, "isHomeEnable", defaults["isHomeEnable"] as Boolean),
            "isAppLogoForDarkmode" to booleanOption(options, "isAppLogoForDarkmode", defaults["isAppLogoForDarkmode"] as Boolean),
            "appLogoUrl" to stringOption(options, "appLogoUrl"),
            "darkAppLogoUrl" to stringOption(options, "darkAppLogoUrl"),
            "placeHolderImage" to stringOption(options, "placeHolderImage"),
            "appBarBackgroundColor" to colorOption(options, "appBarBackgroundColor", defaults["appBarBackgroundColor"] as String),
            "titleColor" to colorOption(options, "titleColor", defaults["titleColor"] as String),
            "titleFontSize" to intOption(options, "titleFontSize", defaults["titleFontSize"] as Int, 12),
            "titleFontWeight" to (stringOption(options, "titleFontWeight") ?: defaults["titleFontWeight"]),
            "logoWidth" to intOption(options, "logoWidth", defaults["logoWidth"] as Int, 20),
            "logoHeight" to intOption(options, "logoHeight", defaults["logoHeight"] as Int, 20)
        )
    }

    private fun defaultOptions(): Map<String, Any> {
        return mapOf(
            "showAppLogo" to false,
            "isElevated" to true,
            "isLeadingEnable" to false,
            "isHomeEnable" to false,
            "isAppLogoForDarkmode" to false,
            "appLogoUrl" to "",
            "darkAppLogoUrl" to "",
            "placeHolderImage" to "",
            "appBarBackgroundColor" to "#ffffff",
            "titleColor" to "#111827",
            "titleFontSize" to 18,
            "titleFontWeight" to "600",
            "logoWidth" to 32,
            "logoHeight" to 32
        )
    }

    private fun booleanOption(options: Map<*, *>, key: String, fallback: Boolean): Boolean {
        return options[key] as? Boolean ?: fallback
    }

    private fun stringOption(options: Map<*, *>, key: String): String? {
        val value = (options[key] as? String)?.trim().orEmpty()
        return if (value.isEmpty()) null else value
    }

    private fun intOption(options: Map<*, *>, key: String, fallback: Int, minimum: Int): Int {
        val value = when (val raw = options[key]) {
            is Int -> raw
            is Double -> raw.toInt()
            is Float -> raw.toInt()
            else -> fallback
        }

        return maxOf(minimum, value)
    }

    private fun colorOption(options: Map<*, *>, key: String, fallback: String): String {
        val value = stringOption(options, key) ?: return fallback
        val hexRegex = Regex("^#[0-9a-fA-F]{3,8}$")
        val cssFunctionRegex = Regex("^(rgb|rgba|hsl|hsla)\\([^)]+\\)$")

        return if (hexRegex.matches(value) || cssFunctionRegex.matches(value)) value else fallback
    }
}
